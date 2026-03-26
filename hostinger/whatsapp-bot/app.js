/**
 * Orbit WhatsApp Bot - Hostinger Entry Point
 * 
 * Entry point untuk Node.js App di Hostinger hPanel.
 * Hostinger membutuhkan file "app.js" sebagai entry point.
 * 
 * File ini membaca .env dari folder orbit-app Laravel 
 * agar credential database & konfigurasi tetap satu tempat.
 */

import dotenv from 'dotenv';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// ============================================
// Load .env dari orbit-app (Laravel)
// Urutan prioritas:
// 1. .env di folder whatsapp-bot/ ini (jika ada)
// 2. .env di orbit-app/ (shared with Laravel)
// ============================================
const localEnv = path.join(__dirname, '.env');
const laravelEnv = path.join(__dirname, '..', 'orbit-app', '.env');

if (fs.existsSync(localEnv)) {
    console.log('Loading .env from whatsapp-bot/.env');
    dotenv.config({ path: localEnv });
} else if (fs.existsSync(laravelEnv)) {
    console.log('Loading .env from orbit-app/.env (shared with Laravel)');
    dotenv.config({ path: laravelEnv });
} else {
    console.log('No .env found, using environment variables');
    dotenv.config();
}

// ============================================
// Import & start the actual WhatsApp server
// ============================================
import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import cors from 'cors';
import WhatsAppManager from './src/WhatsAppManager.js';

const app = express();
const server = http.createServer(app);

// Determine allowed origins
const laravelUrl = process.env.WHATSAPP_LARAVEL_URL || process.env.APP_URL || 'https://orbitwaapi.dpdns.org';
const allowedOrigins = [
    laravelUrl,
    process.env.APP_URL,
    'http://localhost:3000',
    'http://localhost:8000'
].filter(Boolean);

const io = new Server(server, {
    cors: {
        origin: allowedOrigins,
        methods: ['GET', 'POST'],
        credentials: true
    }
});

// Middleware
app.use(cors({
    origin: allowedOrigins,
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

app.get('/', (req, res) => {
    res.json({ 
        service: 'Orbit WhatsApp Bot',
        status: 'running',
        timestamp: new Date().toISOString()
    });
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
const PORT = process.env.WHATSAPP_PORT || 3001;
server.listen(PORT, '0.0.0.0', async () => {
    console.log(`WhatsApp Service running on port ${PORT}`);
    console.log(`Laravel URL: ${laravelUrl}`);
    console.log(`Health check: http://localhost:${PORT}/health`);

    // Auto-restore saved sessions on startup
    await waManager.restoreSessions();
});

// Graceful shutdown
process.on('SIGTERM', () => {
    console.log('SIGTERM received. Shutting down...');
    server.close(() => process.exit(0));
});

process.on('SIGINT', () => {
    console.log('SIGINT received. Shutting down...');
    server.close(() => process.exit(0));
});
