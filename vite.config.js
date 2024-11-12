import { defineConfig } from "vite"

export default defineConfig({
  publicDir: "./resources/static",
  esbuild: { keepNames: true },
  build: {
    assetsDir: "",
    emptyOutDir: true,
    manifest: true,
    outDir: "./src/assets",
    terserOptions: { keep_classnames: /Element$/ },
    rollupOptions: {
      input: {
        js: "./resources/js/index.ts",
        css: "./resources/css/index.css",
        adminJs: "./resources/js/admin/index.ts",
        adminCss: "./resources/css/admin/index.css",
      },
    },
  },
})
