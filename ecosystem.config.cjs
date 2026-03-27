const path = require('path');
const os = require('os');

const isWindows = os.platform() === 'win32';
const rootDir = __dirname;
const cloudflaredPath = isWindows ? 'cloudflared' : '/usr/local/bin/cloudflared';
const laravelPort = 8000;

module.exports = {
  apps: [
    // Remove vite-dev for production (assets should be pre-built)
    {
      name: 'whatsapp-service',
      script: 'bot/server.js',
      cwd: rootDir,
      env: {
        NODE_ENV: 'production',
      },
      watch: false,
      autorestart: true,
    },
    // ============================================
    // QUEUE WORKERS - Priority-based message queues
    // ============================================
    {
      name: 'queue-high',
      script: 'php',
      args: 'artisan queue:work --queue=high --tries=2 --timeout=30 --sleep=1',
      cwd: rootDir,
      instances: 2,
      autorestart: true,
      max_restarts: 10,
    },
    {
      name: 'queue-default',
      script: 'php',
      args: 'artisan queue:work --queue=default --tries=3 --timeout=60 --sleep=3',
      cwd: rootDir,
      instances: 1,
      autorestart: true,
      max_restarts: 10,
    },
    {
      name: 'queue-low',
      script: 'php',
      args: 'artisan queue:work --queue=low --tries=3 --timeout=120 --sleep=5',
      cwd: rootDir,
      instances: 1,
      autorestart: true,
      max_restarts: 10,
    },
    // ============================================
    // SCHEDULER - Runs scheduled tasks
    // ============================================
    {
      name: 'scheduler',
      script: 'php',
      args: 'artisan schedule:work',
      cwd: rootDir,
      instances: 1,
      autorestart: true,
    },
    // Remove laravel-web (artisan serve) for production (served via Nginx/FPM)
  ]
};
