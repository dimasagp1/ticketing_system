import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,jsx,ts,tsx,vue}',
    ],

    theme: {
        extend: {
            colors: {
                // TOONWORLD Color Palette Tokens
                'toon-yellow': '#FFE600',
                'toon-blue': '#0055FF',
                'toon-pink': '#FF007A',
                'toon-orange': '#FF6B00',
                'toon-green': '#00E676',
                'toon-cream': '#FFFBEA',
                'toon-purple': '#9000FF',
                'toon-black': '#000000',
                'toon-white': '#FFFFFF',
                'toon-dark': '#000000',
                'toon-dark-card': '#121212',
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Nunito', ...defaultTheme.fontFamily.sans],
                fredoka: ['Fredoka', 'cursive', 'sans-serif'],
                jakarta: ['Plus Jakarta Sans', 'sans-serif'],
            },
            boxShadow: {
                'brutal-sm': '3px 3px 0px 0px #000000',
                'brutal': '6px 6px 0px 0px #000000',
                'brutal-lg': '10px 10px 0px 0px #000000',
                'brutal-xl': '14px 14px 0px 0px #000000',
                'brutal-dark': '6px 6px 0px 0px #FFE600',
                'brutal-dark-lg': '10px 10px 0px 0px #FFE600',
                'brutal-pink': '6px 6px 0px 0px #FF007A',
                'brutal-yellow': '6px 6px 0px 0px #FFE600',
                'brutal-blue': '6px 6px 0px 0px #0055FF',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
                '4xl': '2rem',
            },
            borderWidth: {
                '3': '3px',
                '4': '4px',
                '6': '6px',
            },
            keyframes: {
                floatSlow: {
                    '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                    '50%': { transform: 'translateY(-12px) rotate(2deg)' },
                },
                wobble: {
                    '0%, 100%': { transform: 'rotate(-3deg)' },
                    '50%': { transform: 'rotate(3deg)' },
                },
                bounceSoft: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-6px)' },
                }
            },
            animation: {
                'float-slow': 'floatSlow 4s ease-in-out infinite',
                'wobble': 'wobble 2s ease-in-out infinite',
                'bounce-soft': 'bounceSoft 1.5s ease-in-out infinite',
            }
        },
    },

    plugins: [forms],
};
