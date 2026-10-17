import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import AutoImport from 'unplugin-auto-import/vite';
import path from 'path';


const buildVersion = process.env.VITE_BUILD_VERSION || Date.now()


export default defineConfig({
    base: '/',
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),

        AutoImport({
            imports: [
                'vue',
                {
                    '@/composables/usePaths': ['usePaths'],
                },
            ],
            dts: 'src/auto-imports.d.ts',
        }),
    ],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'src'),
        },
    },

    // build: {
    //     chunkSizeWarningLimit: 4000,
    //     minify: true,
    //     outDir: 'dist',
    // },
    build: {
        chunkSizeWarningLimit: 4000,
        minify: true,
        outDir: 'dist',
       // outDir: `public/build/${buildVersion}`,
        rollupOptions: {
            output: {
                entryFileNames: `assets/[name].${buildVersion}.[hash].js`,
            }
        }
    }
});