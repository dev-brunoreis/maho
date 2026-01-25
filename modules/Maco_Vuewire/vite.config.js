import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'skin/frontend/maco/vuewire/js'),
    },
  },
  build: {
    manifest: true,
    cssCodeSplit: true,
    outDir: path.resolve(__dirname, 'skin/frontend/maco/vuewire/js/dist'),
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'skin/frontend/maco/vuewire/js/main.js'),
      },
      output: {
        format: 'es',
        inlineDynamicImports: true,
        entryFileNames: 'app.js',
        assetFileNames: 'app.[ext]'
      }
    }
  }
})