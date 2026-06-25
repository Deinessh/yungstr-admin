import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                cream: {
                    DEFAULT: '#FFFFFF',
                    dark: '#E8F5EE',
                    section: '#F8FBF9',
                    bar: '#E8F5EE',
                },
                brand: {
                    green: '#004D26',
                    'green-logo': '#004D26',
                    'green-light': '#006837',
                    'green-soft': '#E8F5EE',
                    orange: '#F26A2E',
                    'orange-logo': '#F26A2E',
                    'orange-dark': '#D94E22',
                    rose: '#E89BAC',
                    gold: '#C8A24D',
                    amber: '#F26A2E',
                    'amber-dark': '#D94E22',
                    dark: '#1A3324',
                    body: '#1A3324',
                    muted: '#3D5A48',
                    brown: '#1A3324',
                    'brown-light': '#3D5A48',
                    chocolate: '#1A3324',
                },
                admin: {
                    sidebar: '#0F172A',
                    'sidebar-hover': 'rgba(34,197,94,0.12)',
                    'sidebar-active': '#22C55E',
                    main: '#F9FAFB',
                    green: '#22C55E',
                    'green-light': '#DCFCE7',
                    orange: '#22C55E',
                    'orange-light': '#DCFCE7',
                    cream: '#F9FAFB',
                    peach: '#FFFFFF',
                    'peach-light': '#FFFFFF',
                    'orange-bright': '#16A34A',
                },
                whatsapp: '#25D366',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            boxShadow: {
                card: '0 1px 3px rgba(58, 31, 18, 0.06)',
                soft: '0 8px 30px rgba(58, 31, 18, 0.08)',
            },
        },
    },

    plugins: [forms],
};
