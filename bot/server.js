import './polyfill.js';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load .env from project root
dotenv.config({ path: path.join(__dirname, '..', '.env') });
import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import cors from 'cors';
import WhatsAppManager from './WhatsAppManager.js';

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: [
            process.env.WHATSAPP_LARAVEL_URL,
            'https://orbitwaapi.site',
            'https://bot.orbitwaapi.site',
            'http://76.13.20.150:8080',
            'http://76.13.20.150',
            'http://localhost:3000',
            'http://localhost:8000'
        ].filter(Boolean),
        methods: ['GET', 'POST'],
        credentials: true
    }
});

// Middleware
app.use(cors({
    origin: [
        process.env.WHATSAPP_LARAVEL_URL,
        'https://orbitwaapi.site',
        'https://bot.orbitwaapi.site',
        'http://76.13.20.150:8080',
        'http://76.13.20.150',
        'http://localhost:3000',
        'http://localhost:8000'
    ].filter(Boolean),
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept'],
    credentials: true
}));
app.use(express.json());

// WhatsApp Manager instance
const waManager = new WhatsAppManager(io);

// Health check
app.get('/health', (req, res) => {
    res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

// Device routes
app.get('/device/:deviceId/qr', async (req, res) => {
    try {
        const { deviceId } = req.params;
        const qr = await waManager.getQR(deviceId);
        res.json({ success: true, qr });
    } catch (error) {
        res.status(400).json({ success: false, error: error.message });
    }
});

app.post('/device/:deviceId/pairing-code', async (req, res) => {
    try {
        const { deviceId } = req.params;
        const { phone } = req.body;
        if (!phone) throw new Error('Phone number is required');

        const code = await waManager.getPairingCode(deviceId, phone);
        res.json({ success: true, pairingCode: code });
    } catch (error) {
        res.status(400).json({ success: false, error: error.message });
    }
});

app.post('/device/:deviceId/connect', async (req, res) => {
    try {
        const { deviceId } = req.params;
        const { userId, name } = req.body;
        await waManager.initSession(deviceId, userId, name);
        res.json({ success: true, message: 'Session initialization started' });
    } catch (error) {
        res.status(400).json({ success: false, error: error.message });
    }
});

app.post('/device/:deviceId/disconnect', async (req, res) => {
    try {
        const { deviceId } = req.params;
        await waManager.disconnectSession(deviceId);
        res.json({ success: true, message: 'Session disconnected' });
    } catch (error) {
        res.status(400).json({ success: false, error: error.message });
    }
});

app.get('/device/:deviceId/status', (req, res) => {
    const { deviceId } = req.params;
    const status = waManager.getSessionStatus(deviceId);
    res.json({ success: true, ...status });
});

// Message routes
app.post('/message/send', async (req, res) => {
    try {
        const { deviceId, phone, message, type = 'text', mediaUrl } = req.body;
        const result = await waManager.sendMessage(deviceId, phone, message, type, mediaUrl);
        res.json({ success: true, ...result });
    } catch (error) {
        res.status(400).json({ success: false, error: error.message });
    }
});

// Socket.IO events
io.on('connection', (socket) => {
    console.log('Laravel connected:', socket.id);

    socket.on('subscribe', (deviceId) => {
        socket.join(`device:${deviceId}`);
        console.log(`Socket ${socket.id} subscribed to device:${deviceId}`);
    });

    socket.on('unsubscribe', (deviceId) => {
        socket.leave(`device:${deviceId}`);
    });

    socket.on('disconnect', () => {
        console.log('Laravel disconnected:', socket.id);
    });
});

// Start server
const PORT = parseInt(process.env.WHATSAPP_PORT) || 3005;
console.log(`Starting WhatsApp Service...`);
console.log(`Configured PORT: ${PORT}`);
console.log(`From process.env.WHATSAPP_PORT: ${process.env.WHATSAPP_PORT}`);

server.listen(PORT, '0.0.0.0', async () => {
    console.log(`WhatsApp Service running on port ${PORT}`);
    console.log(`Health check: http://localhost:${PORT}/health`);

    // Auto-restore saved sessions on startup
    await waManager.restoreSessions();
});
