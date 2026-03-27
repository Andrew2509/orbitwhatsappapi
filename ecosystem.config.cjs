module.exports = {
  apps: [
    {
      name: 'vite-dev',
      script: 'node_modules/.bin/vite',
      cwd: 'c:/laragon/www/orbit-whatsapp-api',
      watch: false,
      autorestart: true,
    },
    {
      name: 'whatsapp-service',
      script: 'bot/server.js',
      cwd: './',
      env: {
        NODE_ENV: 'production',
      },
      watch: false,
      autorestart: true,
    },
    {
      name: 'cloudflare-tunnel',
      script: 'cloudflared',
      args: 'tunnel run --token eyJhIjoiNjUxMmNlMTFkOWU5ZDc2NmUwMDkzOTgyOGM0Y2U4MDUiLCJ0IjoiMGViZWYyZTMtZjVjZS00ODE5LWJkZDQtY2E0MzY3OGJhZDc1IiwicyI6ImFLeVMxb3hXeVZ0bDZ4T1ROcER4WlkwNEtkRS8yckRoMHo1Q05mNnZ2cG89In0= --protocol http2',
      autorestart: true,
      max_restarts: 10,
    },
    // ============================================
    // QUEUE WORKERS - Priority-based message queues
    // ============================================
    {
      name: 'queue-high',
      script: 'php',
      args: 'artisan queue:work --queue=high --tries=2 --timeout=30 --sleep=1',
      cwd: 'c:/laragon/www/orbit-whatsapp-api',
      instances: 2,
      autorestart: true,
      max_restarts: 10,
    },
    {
      name: 'queue-default',
      script: 'php',
      args: 'artisan queue:work --queue=default --tries=3 --timeout=60 --sleep=3',
      cwd: 'c:/laragon/www/orbit-whatsapp-api',
      instances: 1,
      autorestart: true,
      max_restarts: 10,
    },
    {
      name: 'queue-low',
      script: 'php',
      args: 'artisan queue:work --queue=low --tries=3 --timeout=120 --sleep=5',
      cwd: 'c:/laragon/www/orbit-whatsapp-api',
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
      cwd: 'c:/laragon/www/orbit-whatsapp-api',
      instances: 1,
      autorestart: true,
    }
  ]
};

