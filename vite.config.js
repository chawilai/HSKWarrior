import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import svgLoader from 'vite-svg-loader'
// import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            ssr: "resources/js/ssr.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        svgLoader(),
        tailwindcss(),
    ],
    // resolve: {
    //     alias: {
    //         "@": path.resolve(__dirname, "resources/js"),
    //     },
    // },
});
