import { defineConfig } from 'vite';
import fs from "fs";
import path from "path";

// Kopierar manifest.json till dist från .vite.
function moveManifest() {
    return {
        name: "move-manifest",
        closeBundle() {
            const viteDir = path.resolve(__dirname, "dist", ".vite");
            const manifestSrc = path.join(viteDir, "manifest.json");
            const manifestDest = path.resolve(__dirname, "dist", "manifest.json");

            if (fs.existsSync(manifestSrc)) {
                fs.copyFileSync(manifestSrc, manifestDest);
                console.log("✔ Moved manifest.json to dist/");
            } else {
                console.log("✖ No manifest found in .vite/");
            }
        }
    };
}

export default defineConfig({
    root: 'src',
    build: {
        outDir: '../dist',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: "./src/main.js",
            output: {
                assetFileNames: "assets/[name]-[hash][extname]",
                entryFileNames: "assets/[name]-[hash].js",
                chunkFileNames: "assets/[name]-[hash].js",
            }
        },
    },
    plugins: [moveManifest()],
    server: {
        strictPort: true,
        port: 5173,
        hmr: { host: 'localhost' },
    },
});