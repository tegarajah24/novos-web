import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    // Matikan atau hapus blok server jika hanya akses lewat 127.0.0.1
    // server: {
    //     host: '0.0.0.0',
    //     hmr: {
    //         host: '192.168.18.50',
    //     },
    // },
});