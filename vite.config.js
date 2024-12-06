import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],  // Menyertakan CSS dan JS untuk kompilasi
            refresh: true,  // Agar halaman ter-refresh saat file berubah
        }),
    ],
});
