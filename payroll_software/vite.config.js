import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
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
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        // manifest: true,
        // This forces the manifest to be created at public/build/manifest.json
        manifest: 'manifest.json',
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        // Put all third-party libraries in a 'vendor' chunk
                        return 'vendor';
                    }
                }
            }
        },
        chunkSizeWarningLimit: 1000, // Optional: Raise the limit to 1MB if you're okay with it

    },
    css: {
        devSourcemap: false, // This stops the warnings during 'npm run dev'
    }
});
