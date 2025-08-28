// tailwind.config.js
const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'brand-primary': '#D32F2F', // Your #D32F2F
                'brand-secondary': '#C2185B', // Your #C2185B
                'brand-accent': '#ef4444',    // Your #ef4444
                'dark-text': '#2a2a2a',
                'light-text': '#f0f0f0',
                'bg-light': '#f8f8f8',
                'subtle-gray': '#e0e0e0',
            },
            fontFamily: {
                sans: ['"Nunito Sans"', ...defaultTheme.fontFamily.sans], // A clean, modern sans-serif font
                heading: ['"Montserrat"', 'sans-serif'], // A bold, impactful heading font
            },
            keyframes: {
                slideInLeft: {
                    '0%': { transform: 'translateX(-100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                slideInRight: {
                    '0%': { transform: 'translateX(100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                fadeInScale: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                bounceSubtle: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
                rotatePulse: {
                    '0%': { transform: 'rotate(0deg) scale(1)' },
                    '50%': { transform: 'rotate(180deg) scale(1.05)' },
                    '100%': { transform: 'rotate(360deg) scale(1)' },
                },
                glow: {
                    '0%, 100%': { boxShadow: '0 0 5px rgba(239, 68, 68, 0.5)' },
                    '50%': { boxShadow: '0 0 20px rgba(239, 68, 68, 0.8)' },
                },
                spotlight: {
                    '0%': { transform: 'translateX(-100%) translateY(-100%)' },
                    '100%': { transform: 'translateX(100%) translateY(100%)' },
                },
            },
            animation: {
                'slide-in-left': 'slideInLeft 0.8s ease-out forwards',
                'slide-in-right': 'slideInRight 0.8s ease-out forwards',
                'fade-in-scale': 'fadeInScale 0.7s ease-out forwards',
                'bounce-subtle': 'bounceSubtle 2s infinite ease-in-out',
                'rotate-pulse': 'rotatePulse 1.5s infinite ease-in-out',
                'glow-effect': 'glow 1.5s infinite alternate',
                'spotlight-shine': 'spotlight 2s linear infinite',
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};