import * as pkg from '@whiskeysockets/baileys';
const { initAuthCreds, BufferJSON } = pkg;
import mysql from 'mysql2/promise';

let sharedPool = null;

const getPool = (dbConfig) => {
    if (!sharedPool) {
        sharedPool = mysql.createPool({
            ...dbConfig,
            waitForConnections: true,
            connectionLimit: 10,
            maxIdle: 10,
            idleTimeout: 60000,
            queueLimit: 0,
            enableKeepAlive: true,
            keepAliveInitialDelay: 0,
            connectTimeout: 60000
        });
    }
    return sharedPool;
};

export const getAllSessionIds = async (dbConfig) => {
    try {
        const pool = getPool(dbConfig);
        const [rows] = await pool.query('SELECT DISTINCT session_id FROM whatsapp_sessions');
        return rows.map(r => r.session_id);
    } catch (error) {
        console.error('[MySQL] Error getting session IDs:', error);
        return [];
    }
};

export const useMySQLAuthState = async (sessionId, dbConfig) => {
    const pool = getPool(dbConfig);

    // Fungsi membaca data dari database
    const readData = async (file) => {
        try {
            const [rows] = await pool.query(
                'SELECT data FROM whatsapp_sessions WHERE session_id = ? AND file = ? LIMIT 1',
                [sessionId, file]
            );

            if (rows.length > 0 && rows[0].data) {
                return JSON.parse(rows[0].data, BufferJSON.reviver);
            }
            return null;
        } catch (error) {
            console.error(`[MySQL] Error reading ${file}:`, error);
            return null;
        }
    };

    // Fungsi menulis data ke database
    const writeData = async (data, file) => {
        try {
            const dataStr = JSON.stringify(data, BufferJSON.replacer);
            await pool.query(
                `INSERT INTO whatsapp_sessions (session_id, file, data, created_at, updated_at)
                 VALUES (?, ?, ?, NOW(), NOW())
                 ON DUPLICATE KEY UPDATE data = VALUES(data), updated_at = NOW()`,
                [sessionId, file, dataStr]
            );
        } catch (error) {
            console.error(`[MySQL] Error writing ${file}:`, error);
        }
    };

    // Fungsi menghapus satu record
    const removeData = async (file) => {
        try {
            await pool.query(
                'DELETE FROM whatsapp_sessions WHERE session_id = ? AND file = ?',
                [sessionId, file]
            );
        } catch (error) {
            console.error(`[MySQL] Error removing ${file}:`, error);
        }
    };

    // Menghapus keseluruhan sesi
    const removeSession = async () => {
        try {
            await pool.query(
                'DELETE FROM whatsapp_sessions WHERE session_id = ?',
                [sessionId]
            );
        } catch (error) {
            console.error(`[MySQL] Error removing complete session:`, error);
        }
    }

    // Inisialisasi kredensial utama
    const creds = await readData('creds.json') || initAuthCreds();

    return {
        state: {
            creds,
            keys: {
                get: async (type, ids) => {
                    const data = {};
                    await Promise.all(
                        ids.map(async (id) => {
                            let value = await readData(`${type}-${id}.json`);
                            if (type === 'app-state-sync-key' && value) {
                                value = pkg.proto.Message.AppStateSyncKeyData.fromObject(value);
                            }
                            data[id] = value;
                        })
                    );
                    return data;
                },
                set: async (data) => {
                    const tasks = [];
                    for (const category in data) {
                        for (const id in data[category]) {
                            const value = data[category][id];
                            const file = `${category}-${id}.json`;
                            if (value) {
                                tasks.push(writeData(value, file));
                            } else {
                                tasks.push(removeData(file));
                            }
                        }
                    }
                    await Promise.all(tasks);
                },
            },
        },
        saveCreds: () => {
            return writeData(creds, 'creds.json');
        },
        removeSession
    };
};
