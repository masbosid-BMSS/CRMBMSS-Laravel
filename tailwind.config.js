import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', 'Arial', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                bmss: {
                    navy: '#343A72',
                    navyDark: '#252B5B',
                    navySoft: '#EEF0F8',
                    red: '#FF303B',
                    redSoft: '#FFF0F2',
                    gold: '#FFC316',
                    goldSoft: '#FFF7D8',
                    green: '#1F9D68',
                    greenSoft: '#EAF8F1',
                    orange: '#C98600',
                    orangeSoft: '#FFF5DD',
                    bg: '#F8F8FB',
                    line: '#E1E3EC',
                    text: '#242842',
                    muted: '#697087',
                }
            },
            boxShadow: {
                'bmss': '0 12px 30px rgba(52, 58, 114, 0.1)',
                'bmss-sm': '0 6px 18px rgba(52, 58, 114, 0.08)',
            },
            borderRadius: {
                'bmss': '18px',
                'bmss-card': '24px',
            }
        },
    },
    plugins: [forms],
};
