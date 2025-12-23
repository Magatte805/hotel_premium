import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'dist', // dossier généré
    emptyOutDir: true,
  },
  base: '/', // très important pour que les assets soient trouvés par Symfony
});
