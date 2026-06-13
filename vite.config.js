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
    // PASTIKAN BLOK INI ADA & IP-NYA SUDAH SESUAI:
    server: {
        host: '0.0.0.0', // Mengizinkan semua perangkat di Wi-Fi yang sama untuk akses
        hmr: {
            host: '192.168.18.50', // IP Laptop kamu
        },
    },
});