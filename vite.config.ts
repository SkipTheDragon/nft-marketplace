import {defineConfig} from 'vite'
import symfonyPlugin from "vite-plugin-symfony";
import viteReact from "@vitejs/plugin-react";
import fosRoutingPlugin from "./assets/vite-plugins/fosRoutingPlugin";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        viteReact(),
        symfonyPlugin(),
        fosRoutingPlugin({
            verbose: true
        })
    ],
    build: {
        rollupOptions: {
            input: {
                app: "./assets/src/main.tsx"
            },
        }
    },
    server: {
        watch: {
            usePolling: true,
        },
        host: '0.0.0.0',
        origin: 'http://localhost:5173'
    }
})
