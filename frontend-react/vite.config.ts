import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000,
    proxy: {
      // proxy API calls to existing Laravel backend (adjust URL in .env or here)
      '/api': {
        target: process.env.BACKEND_URL || 'http://localhost:8000',
        changeOrigin: true,
        secure: false
      }
    }
  },
  build: {
    outDir: '../public_html/frontend', // optional: place build inside project public folder
    emptyOutDir: false
  }
});
