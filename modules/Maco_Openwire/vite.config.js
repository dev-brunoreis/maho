import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    lib: {
      entry: resolve(__dirname, 'js/openwire/src/index.ts'),
      name: 'OpenWire',
      fileName: 'openwire',
      formats: ['iife']
    },
    outDir: 'js/openwire/dist',
    rollupOptions: {
      output: {
        entryFileNames: 'openwire.js'
      }
    }
  }
});