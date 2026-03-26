import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
              primary: "#10B981", // Emerald 500
              "primary-dark": "#059669", // Emerald 600
              "background-light": "#F8FAFC", // Slate 50
              "background-dark": "#0F172A", // Slate 900
              "surface-light": "#FFFFFF",
              "surface-dark": "#1E293B", // Slate 800
              "accent-navy": "#1e293b",
              "text-light": "#1F2937",
              "text-dark": "#F3F4F6",
              "muted-light": "#6B7280",
              "muted-dark": "#9CA3AF",
            },
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', 'sans-serif'],
                mono: ['Fira Code', 'monospace'],
            },
            borderRadius: {
                DEFAULT: '0.375rem',
                'xl': '0.75rem',
                '2xl': '1rem',
            },
            boxShadow: {
                'glow': '0 0 20px -5px rgba(16, 185, 129, 0.3)',
                'glow-lg': '0 0 30px -5px rgba(16, 185, 129, 0.4)',
                'glow-emerald': '0 0 25px 5px rgba(16, 185, 129, 0.5)',
                'card-hover': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04), 0 0 15px rgba(16, 185, 129, 0.2)',
            },
            animation: {
                'float': 'float 6s ease-in-out infinite',
                'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'scan': 'scan 8s linear infinite',
                'shimmer': 'shimmer 2s linear infinite',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                scan: {
                    '0%': { transform: 'translateY(-100%)' },
                    '100%': { transform: 'translateY(100%)' },
                },
                shimmer: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                }
            }
        },
    },

    plugins: [forms, typography],
};
