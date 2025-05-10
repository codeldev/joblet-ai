import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import {resolve} from 'path';
import {rm} from 'node:fs/promises';
import terser from '@rollup/plugin-terser';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(
    {
        base: '/',
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/css/invoice.css'
                ],
                refresh: true,
            }),
            tailwindcss(),
            {
                name: "Cleaning JS folder",
                async buildStart()
                {
                    await rm(resolve(__dirname, 'public/assets/js'), {recursive: true, force: true});
                }
            },
            {
                name: "Cleaning CSS folder",
                async buildStart()
                {
                    await rm(resolve(__dirname, 'public/assets/css'), {recursive: true, force: true});
                }
            }
        ],
        build: {
            minify: 'terser',
            chunkSizeWarningLimit: 1500,
            outDir: 'public',
            emptyOutDir: false,
            rollupOptions: {
                plugins: [terser({
                                     format: { comments: false, },
                                 })],
                output: {
                    assetFileNames: (assetInfo) =>
                    {
                        let extType = assetInfo.name.split('.').at(1);
                        let assetName = assetInfo.name.split('.').at(0);

                        if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType))
                        {
                            extType = 'img';
                        }

                        if (extType === 'css')
                        {
                            return `assets/css/[name]-[hash].min.css`;
                        }

                        return `assets/${extType}/[name][extname]`;
                    },
                    chunkFileNames: 'assets/js/[name]-[hash].min.js',
                    entryFileNames: 'assets/js/[name]-[hash].min.js',
                },
            },
        },
    });
