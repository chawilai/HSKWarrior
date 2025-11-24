import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                green: {
                    DEFAULT: "#71B214",
                },
                red: {
                    DEFAULT: "#B71F1F",
                },
                gray: {
                    600: "#606060",
                },
            },
        },
    },
    plugins: [],
};
