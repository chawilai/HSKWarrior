import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Prompt', 'Figtree', 'Source Code Pro', ...defaultTheme.fontFamily.sans],

            },
            keyframes: {
                'up-down': {
                  '0%, 100%': { transform: 'translateY(0)' },
                  '50%': { transform: 'translateY(-20px)' },
                },
              },
              animation: {
                'up-down': 'up-down 3s ease-in-out infinite',
              },
        },
    },

    plugins: [forms],
};
