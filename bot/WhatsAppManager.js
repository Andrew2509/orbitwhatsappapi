import makeWASocket, { DisconnectReason, useMultiFileAuthState } from '@whiskeysockets/baileys';
import QRCode from 'qrcode';
import pino from 'pino';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { useMySQLAuthState, getAllSessionIds } from './useMySQLAuthState.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

class WhatsAppManager {
    constructor(io) {
        this.io = io;
        this.sessions = new Map();
        this.qrCodes = new Map();

        this.dbConfig = {
            host: process.env.DB_HOST || '127.0.0.1',
            user: process.env.DB_USERNAME || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_DATABASE || 'laravel',
            port: process.env.DB_PORT || 3306,
            waitForConnections: true,
            connectionLimit: 10,
            queueLimit: 0,
            connectTimeout: 60000 // 60 seconds
        };

        // Jika ada ssl (TiDB)
        if (process.env.MYSQL_ATTR_SSL_CA) {
            this.dbConfig.ssl = { rejectUnauthorized: false };
        }
    }

    /**
     * Restore all existing sessions from saved credentials
     */
    async restoreSessions() {
        console.log('Restoring saved sessions...');

        try {
            const sessionFolders = await getAllSessionIds(this.dbConfig);

            if (sessionFolders.length === 0) {
                console.log('No saved sessions found.');
                // Sync with Laravel - no active devices
                await this.syncDeviceStatuses([]);
                return;
            }

            console.log(`Found ${sessionFolders.length} saved session(s).`);

            for (const deviceId of sessionFolders) {
                console.log(`Restoring session ${deviceId}...`);

                try {
                    // Initialize session with saved credentials
                    await this.initSession(parseInt(deviceId), null, `Restored Device ${deviceId}`);
                } catch (err) {
                    console.error(`Failed to restore session ${deviceId}:`, err.message);
                }
            }

            console.log('Session restoration complete.');

            // Wait a bit for connections to establish
            await new Promise(resolve => setTimeout(resolve, 5000));

            // Get list of actually connected devices and sync with Laravel
            const activeDeviceIds = [];
            for (const [deviceId, session] of this.sessions.entries()) {
                if (session.status === 'connected') {
                    activeDeviceIds.push(deviceId);
                }
            }

            console.log(`Active devices after restore: ${activeDeviceIds.join(', ') || 'none'}`);
            await this.syncDeviceStatuses(activeDeviceIds);

        } catch (error) {
            console.error('Error restoring sessions:', error.message);
        }
    }

    /**
     * Sync device statuses with Laravel
     */
    async syncDeviceStatuses(activeDeviceIds) {
        try {
            console.log('Syncing device statuses with Laravel...');
            await this.notifyLaravel('devices.sync', { activeDeviceIds });
            console.log('Device statuses synced successfully.');
        } catch (error) {
            console.error('Failed to sync device statuses:', error.message);
        }
    }

    async initSession(id, userId, name) {
        const deviceId = parseInt(id);
        console.log(`[${deviceId}] Starting initSession...`);
        // Close existing session if any
        if (this.sessions.has(deviceId)) {
            console.log(`[${deviceId}] Found existing session, closing first...`);
            await this.disconnectSession(deviceId);
        }

        const { state, saveCreds, removeSession } = await useMySQLAuthState(deviceId.toString(), this.dbConfig);

        const sock = makeWASocket({
            auth: state,
            logger: pino({ level: 'warn' }),
            browser: ['Orbit API', 'Chrome', '114.0.5735.199'],
            // version: [2, 3000, 1033846690], // Remove hardcoded version to use latest
            printQRInTerminal: false
        });

        console.log(`[${deviceId}] WASocket created, starting listeners...`);

        // Store session info
        this.sessions.set(deviceId, {
            socket: sock,
            userId,
            name,
            status: 'connecting',
            phone: null,
            isReady: false // Flag to ignore historical messages
        });

        // Connection update handler
        sock.ev.on('connection.update', async (update) => {
            console.log(`Device ${deviceId} connection update:`, JSON.stringify(update));
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                console.log(`QR received for device ${deviceId}, generating image...`);
                // Generate QR code as base64
                const qrBase64 = await QRCode.toDataURL(qr);
                this.qrCodes.set(deviceId, qrBase64);

                // Emit to Laravel via Socket.IO
                this.io.to(`device:${deviceId}`).emit('qr', {
                    deviceId,
                    qr: qrBase64
                });

                this.updateSessionStatus(deviceId, 'waiting_qr');
                console.log(`[${deviceId}] QR generated and status updated`);
            }

            if (connection === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const errorMessage = lastDisconnect?.error?.message;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

                console.log(`[${deviceId}] Disconnected. Status: ${statusCode}, Message: ${errorMessage}, Reconnect: ${shouldReconnect}`);

                // Handle Stream Errored (440) - usually means session is corrupted
                if (shouldReconnect && (statusCode === 440 || errorMessage === 'Stream Errored')) {
                    console.log(`[${deviceId}] Stream Errored/Session Corrupted. Clearing and restarting fresh...`);
                    try {
                        await removeSession();
                    } catch (e) {
                        console.error(`[${deviceId}] Error clearing corrupted session:`, e.message);
                    }
                    // Re-initialize after a short delay
                    setTimeout(() => this.initSession(deviceId, userId, name), 2000);
                    return;
                }

                if (shouldReconnect) {
                    await this.initSession(deviceId, userId, name);
                } else {
                    console.log(`[${deviceId}] Permanent disconnect (logged out). Cleaning up...`);
                    this.updateSessionStatus(deviceId, 'disconnected');
                    
                    // Clear database session
                    try {
                        await removeSession();
                    } catch (err) {
                        console.error(`[${deviceId}] Failed to clear session data:`, err.message);
                    }
                    
                    this.sessions.delete(deviceId);
                    this.qrCodes.delete(deviceId);

                    // Notify Laravel
                    this.io.to(`device:${deviceId}`).emit('disconnected', { deviceId });

                    // Callback to Laravel to update database
                    await this.notifyLaravel('device.disconnected', { deviceId });
                }
            }

            if (connection === 'open') {
                const session = this.sessions.get(deviceId);
                const phone = sock.user?.id?.split(':')[0] || sock.user?.id?.split('@')[0];

                if (session) {
                    session.status = 'connected';
                    session.phone = phone;
                    // Set isReady after a delay to skip all sync messages
                    setTimeout(() => {
                        if (this.sessions.has(deviceId)) {
                            this.sessions.get(deviceId).isReady = true;
                            console.log(`Device ${deviceId} is now ready to receive messages.`);
                        }
                    }, 3000); // Wait 3 seconds for sync to complete
                }

                this.qrCodes.delete(deviceId);

                console.log(`Device ${deviceId} connected! Phone: ${phone}`);

                // Notify Laravel
                this.io.to(`device:${deviceId}`).emit('connected', {
                    deviceId,
                    phone
                });

                await this.notifyLaravel('device.connected', { deviceId, phone });
            }
        });

        // Credentials update handler
        sock.ev.on('creds.update', saveCreds);

        // Message received handler - CHATBOT INTEGRATION
        sock.ev.on('messages.upsert', async (m) => {
            const session = this.sessions.get(deviceId);

            // Skip if session not ready (still syncing historical messages)
            if (!session || !session.isReady) {
                return;
            }

            for (const msg of m.messages) {
                // Extract text from various possible locations
                const text = msg.message?.conversation ||
                             msg.message?.extendedTextMessage?.text ||
                             msg.message?.imageMessage?.caption ||
                             msg.message?.documentMessage?.caption;

                // Skip non-text messages
                if (!text) continue;

                // Skip status broadcasts
                if (msg.key.remoteJid === 'status@broadcast') continue;

                // Skip own messages UNLESS it looks like an approval command (ACC/REJ)
                if (msg.key.fromMe) {
                    // Match ACC/REJ at start, ignoring case and leading non-alphanumeric chars (like symbols)
                    const isCommand = /^[\W_]*(ACC|REJ)\b/i.test(text);
                    if (!isCommand) continue;
                    console.log(`[Device ${deviceId}] Processing self-sent command: ${text}`);
                }
                const remoteJid = msg.key.remoteJid; // Keep original JID for reply

                // Extract phone number for Laravel
                let from = remoteJid.split('@')[0];

                // FIX: If message is from me, remoteJid might be LID (948...) instead of phone.
                // We should use the session's phone number instead to ensure Admin ID matches.
                if (msg.key.fromMe && session.phone) {
                    from = session.phone;
                    console.log(`[Device ${deviceId}] Normalized self-message from ${remoteJid} to ${from}`);
                }

                console.log(`[Device ${deviceId}] Incoming message from ${from}: ${text}`);

                try {
                    // Call Laravel chatbot endpoint
                    const laravelUrl = process.env.WHATSAPP_LARAVEL_URL || 'https://orbitwaapi.dpdns.org';
                    const response = await fetch(`${laravelUrl}/api/webhook/incoming-message`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WhatsApp-Secret': process.env.WHATSAPP_SECRET || 'secret'
                        },
                        body: JSON.stringify({
                            device_id: deviceId,
                            from: from,
                            message: text,
                            message_id: msg.key.id
                        })
                    });

                    const data = await response.json();

                    if (data.success && data.should_reply && data.reply) {
                        console.log(`[Device ${deviceId}] Auto-replying to ${remoteJid}: ${data.reply.substring(0, 50)}...`);

                        // Send the auto-reply directly using the socket and original JID
                        try {
                            await sock.sendMessage(remoteJid, { text: data.reply });
                            console.log(`[Device ${deviceId}] Auto-reply sent successfully to ${from}`);
                        } catch (sendError) {
                            console.error(`[Device ${deviceId}] Failed to send auto-reply:`, sendError.message);
                        }
                    }
                } catch (error) {
                    console.error(`[Device ${deviceId}] Chatbot error:`, error.message);
                }
            }
        });

        // Message status update handler
        sock.ev.on('messages.update', async (updates) => {
            for (const update of updates) {
                const status = update.update?.status;
                if (status) {
                    const statusMap = {
                        2: 'sent',
                        3: 'sent',  // Treat delivered as sent
                        4: 'read'
                    };

                    if (statusMap[status]) {
                        await this.notifyLaravel('message.status', {
                            deviceId,
                            messageId: update.key.id,
                            status: statusMap[status]
                        });
                    }
                }
            }
        });

        return sock;
    }

    async sendMessage(deviceId, phone, message, type = 'text', mediaUrl = null) {
        // Ensure deviceId is an integer (Map keys are stored as integers)
        const id = parseInt(deviceId);
        console.log(`Sending message: device=${id}, phone=${phone}, type=${type}`);

        const session = this.sessions.get(id);
        if (!session) {
            console.log(`Session not found for device ${deviceId}`);
            throw new Error('Device session not found');
        }

        console.log(`Device ${deviceId} session status: ${session.status}`);

        if (session.status !== 'connected') {
            throw new Error(`Device not connected (status: ${session.status})`);
        }

        const sock = session.socket;

        // Check if socket is actually connected
        if (!sock || !sock.user) {
            console.log(`Socket not ready for device ${deviceId}`);
            throw new Error('WhatsApp socket not ready. Please wait or reconnect device.');
        }

        const jid = `${phone.replace(/\D/g, '')}@s.whatsapp.net`;

        console.log(`Sending to JID: ${jid}, Socket user: ${sock.user?.id}`);

        try {
            let result;

            // Create a timeout promise
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Send message timeout (15s)')), 15000);
            });

            let sendPromise;

            if (type === 'text') {
                sendPromise = sock.sendMessage(jid, { text: message });
            } else if (type === 'image' && mediaUrl) {
                // Check if mediaUrl is a local file path or URL
                let imageSource;
                if (mediaUrl.startsWith('http://') || mediaUrl.startsWith('https://')) {
                    imageSource = { url: mediaUrl };
                } else {
                    // Local file path - read directly
                    console.log(`Reading local image file: ${mediaUrl}`);
                    const buffer = fs.readFileSync(mediaUrl);
                    imageSource = buffer;
                }
                sendPromise = sock.sendMessage(jid, {
                    image: imageSource,
                    caption: message
                });
            } else if (type === 'document' && mediaUrl) {
                // Check if mediaUrl is a local file path or URL
                let docSource;
                let fileName = 'document';
                if (mediaUrl.startsWith('http://') || mediaUrl.startsWith('https://')) {
                    docSource = { url: mediaUrl };
                } else {
                    // Local file path - read directly
                    console.log(`Reading local document file: ${mediaUrl}`);
                    const buffer = fs.readFileSync(mediaUrl);
                    docSource = buffer;
                    fileName = path.basename(mediaUrl);
                }
                sendPromise = sock.sendMessage(jid, {
                    document: docSource,
                    fileName: fileName,
                    caption: message
                });
            } else {
                // Default to text if no media URL provided
                sendPromise = sock.sendMessage(jid, { text: message });
            }

            // Race between send and timeout
            result = await Promise.race([sendPromise, timeoutPromise]);
            
            if (!result || !result.key) {
                console.error(`[Device ${deviceId}] sendMessage resolved but result is invalid:`, JSON.stringify(result));
            }

            console.log(`Message sent successfully:`, result?.key?.id);

            return {
                messageId: result?.key?.id || `msg_${Date.now()}`,
                timestamp: Date.now()
            };
        } catch (error) {
            console.error(`Failed to send message:`, error.message);
            throw error;
        }
    }

    async disconnectSession(id) {
        const deviceId = parseInt(id);
        const session = this.sessions.get(deviceId);
        if (session) {
            try {
                console.log(`[${deviceId}] Explicitly disconnecting and logging out...`);
                await session.socket.logout();
                
                // useMySQLAuthState's removeSession implementation
                const { removeSession } = await useMySQLAuthState(deviceId.toString(), this.dbConfig);
                await removeSession();
            } catch (e) {
                console.error(`[${deviceId}] Error during logout:`, e.message);
            }
            this.sessions.delete(deviceId);
            this.qrCodes.delete(deviceId);
        }
    }

    getQR(id) {
        const deviceId = parseInt(id);
        return this.qrCodes.get(deviceId) || null;
    }

    getSessionStatus(id) {
        const deviceId = parseInt(id);
        const session = this.sessions.get(deviceId);
        if (!session) {
            return { status: 'not_initialized', phone: null };
        }
        return {
            status: session.status,
            phone: session.phone,
            pairingCode: session.pairingCode || null
        };
    }

    async getPairingCode(id, phone) {
        const deviceId = parseInt(id);
        let session = this.sessions.get(deviceId);

        // If session is already connected, we don't need a pairing code
        if (session && session.status === 'connected') {
            console.log(`[${deviceId}] Device already connected, skipping pairing code request.`);
            return { error: 'Device already connected' };
        }

        // If no session exists OR it's not in a state where we can request pairing
        if (!session || (session.status !== 'connecting' && session.status !== 'waiting_qr')) {
            console.log(`[${deviceId}] Initializing/Restarting session for pairing code...`);
            await this.initSession(deviceId, null, `Device ${deviceId}`);
            session = this.sessions.get(deviceId);
        }

        console.log(`Requesting pairing code for device ${deviceId}, phone ${phone}`);
        
        try {
            // Clean phone number (remove non-digits)
            const cleanPhone = phone.replace(/\D/g, '');
            
            if (!session.socket) {
                // Wait for socket to be ready (it might be still initializing in initSession)
                console.log(`[${deviceId}] Waiting for socket initialization...`);
                let attempts = 0;
                while (!session.socket && attempts < 40) { // Wait up to 10 seconds
                    await new Promise(resolve => setTimeout(resolve, 250));
                    attempts++;
                }
            }

            if (!session.socket) {
                throw new Error('WhatsApp service is busy initializing. Please try again in a few seconds.');
            }

            // User recommended delay to ensure socket is ready for pairing
            console.log(`[${deviceId}] Waiting 3s for socket to stabilize before pairing request...`);
            await new Promise(resolve => setTimeout(resolve, 3000));

            // Create a timeout for the pairing code request itself
            const pairingCodePromise = session.socket.requestPairingCode(cleanPhone);
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Pairing code request timed out from WhatsApp')), 20000);
            });

            const code = await Promise.race([pairingCodePromise, timeoutPromise]);
            session.pairingCode = code;
            
            // Emit to Laravel via Socket.IO
            this.io.to(`device:${deviceId}`).emit('pairing_code', {
                deviceId,
                pairingCode: code
            });

            return code;
        } catch (error) {
            console.error(`Failed to get pairing code for device ${deviceId}:`, error.message);
            throw error;
        }
    }

    updateSessionStatus(id, status) {
        const deviceId = parseInt(id);
        const session = this.sessions.get(deviceId);
        if (session) {
            session.status = status;
        }

        this.io.to(`device:${deviceId}`).emit('status', { deviceId, status });
    }

    async notifyLaravel(event, data) {
        try {
            const laravelUrl = process.env.WHATSAPP_LARAVEL_URL || 'https://orbitwaapi.dpdns.org';
            const response = await fetch(`${laravelUrl}/api/webhook/whatsapp`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WhatsApp-Secret': process.env.WHATSAPP_SECRET || 'secret'
                },
                body: JSON.stringify({ event, data })
            });
            return response.ok;
        } catch (error) {
            console.error('Failed to notify Laravel:', error.message);
            return false;
        }
    }
}

export default WhatsAppManager;
