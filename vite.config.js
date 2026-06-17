import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                app: 'resources/js/app.js'
            }
        }
    },
    server: {
        strictPort: true,
        port: 5173,
        origin: 'http://localhost:5173'
    }
});
