// vite.config.js
import { defineConfig } from "file:///D:/Soft-Engineering/Web/payroll_software/node_modules/vite/dist/node/index.js";
import laravel from "file:///D:/Soft-Engineering/Web/payroll_software/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///D:/Soft-Engineering/Web/payroll_software/node_modules/@vitejs/plugin-vue/dist/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/js/app.js"
      ],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    })
  ],
  resolve: {
    alias: {
      vue: "vue/dist/vue.esm-bundler.js"
    }
  },
  build: {
    outDir: "public/build",
    emptyOutDir: true,
    // manifest: true,
    // This forces the manifest to be created at public/build/manifest.json
    manifest: "manifest.json",
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes("node_modules")) {
            return "vendor";
          }
        }
      }
    },
    chunkSizeWarningLimit: 1e3
    // Optional: Raise the limit to 1MB if you're okay with it
  },
  css: {
    devSourcemap: false
    // This stops the warnings during 'npm run dev'
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJEOlxcXFxTb2Z0LUVuZ2luZWVyaW5nXFxcXFdlYlxcXFxwYXlyb2xsX3NvZnR3YXJlXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJEOlxcXFxTb2Z0LUVuZ2luZWVyaW5nXFxcXFdlYlxcXFxwYXlyb2xsX3NvZnR3YXJlXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9EOi9Tb2Z0LUVuZ2luZWVyaW5nL1dlYi9wYXlyb2xsX3NvZnR3YXJlL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XHJcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xyXG5pbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSc7XHJcblxyXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xyXG4gICAgcGx1Z2luczogW1xyXG4gICAgICAgIGxhcmF2ZWwoe1xyXG4gICAgICAgICAgICBpbnB1dDogW1xyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9jc3MvYXBwLmNzcycsXHJcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2FwcC5qcycsXHJcbiAgICAgICAgICAgIF0sXHJcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXHJcbiAgICAgICAgfSksXHJcbiAgICAgICAgdnVlKHtcclxuICAgICAgICAgICAgdGVtcGxhdGU6IHtcclxuICAgICAgICAgICAgICAgIHRyYW5zZm9ybUFzc2V0VXJsczoge1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2U6IG51bGwsXHJcbiAgICAgICAgICAgICAgICAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSksXHJcbiAgICBdLFxyXG4gICAgcmVzb2x2ZToge1xyXG4gICAgICAgIGFsaWFzOiB7XHJcbiAgICAgICAgICAgIHZ1ZTogJ3Z1ZS9kaXN0L3Z1ZS5lc20tYnVuZGxlci5qcycsXHJcbiAgICAgICAgfSxcclxuICAgIH0sXHJcbiAgICBidWlsZDoge1xyXG4gICAgICAgIG91dERpcjogJ3B1YmxpYy9idWlsZCcsXHJcbiAgICAgICAgZW1wdHlPdXREaXI6IHRydWUsXHJcbiAgICAgICAgLy8gbWFuaWZlc3Q6IHRydWUsXHJcbiAgICAgICAgLy8gVGhpcyBmb3JjZXMgdGhlIG1hbmlmZXN0IHRvIGJlIGNyZWF0ZWQgYXQgcHVibGljL2J1aWxkL21hbmlmZXN0Lmpzb25cclxuICAgICAgICBtYW5pZmVzdDogJ21hbmlmZXN0Lmpzb24nLFxyXG4gICAgICAgIHJvbGx1cE9wdGlvbnM6IHtcclxuICAgICAgICAgICAgb3V0cHV0OiB7XHJcbiAgICAgICAgICAgICAgICBtYW51YWxDaHVua3MoaWQpIHtcclxuICAgICAgICAgICAgICAgICAgICBpZiAoaWQuaW5jbHVkZXMoJ25vZGVfbW9kdWxlcycpKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIFB1dCBhbGwgdGhpcmQtcGFydHkgbGlicmFyaWVzIGluIGEgJ3ZlbmRvcicgY2h1bmtcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuICd2ZW5kb3InO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0sXHJcbiAgICAgICAgY2h1bmtTaXplV2FybmluZ0xpbWl0OiAxMDAwLCAvLyBPcHRpb25hbDogUmFpc2UgdGhlIGxpbWl0IHRvIDFNQiBpZiB5b3UncmUgb2theSB3aXRoIGl0XHJcblxyXG4gICAgfSxcclxuICAgIGNzczoge1xyXG4gICAgICAgIGRldlNvdXJjZW1hcDogZmFsc2UsIC8vIFRoaXMgc3RvcHMgdGhlIHdhcm5pbmdzIGR1cmluZyAnbnBtIHJ1biBkZXYnXHJcbiAgICB9XHJcbn0pO1xyXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQWtULFNBQVMsb0JBQW9CO0FBQy9VLE9BQU8sYUFBYTtBQUNwQixPQUFPLFNBQVM7QUFFaEIsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDeEIsU0FBUztBQUFBLElBQ0wsUUFBUTtBQUFBLE1BQ0osT0FBTztBQUFBLFFBQ0g7QUFBQSxRQUNBO0FBQUEsTUFDSjtBQUFBLE1BQ0EsU0FBUztBQUFBLElBQ2IsQ0FBQztBQUFBLElBQ0QsSUFBSTtBQUFBLE1BQ0EsVUFBVTtBQUFBLFFBQ04sb0JBQW9CO0FBQUEsVUFDaEIsTUFBTTtBQUFBLFVBQ04saUJBQWlCO0FBQUEsUUFDckI7QUFBQSxNQUNKO0FBQUEsSUFDSixDQUFDO0FBQUEsRUFDTDtBQUFBLEVBQ0EsU0FBUztBQUFBLElBQ0wsT0FBTztBQUFBLE1BQ0gsS0FBSztBQUFBLElBQ1Q7QUFBQSxFQUNKO0FBQUEsRUFDQSxPQUFPO0FBQUEsSUFDSCxRQUFRO0FBQUEsSUFDUixhQUFhO0FBQUE7QUFBQTtBQUFBLElBR2IsVUFBVTtBQUFBLElBQ1YsZUFBZTtBQUFBLE1BQ1gsUUFBUTtBQUFBLFFBQ0osYUFBYSxJQUFJO0FBQ2IsY0FBSSxHQUFHLFNBQVMsY0FBYyxHQUFHO0FBRTdCLG1CQUFPO0FBQUEsVUFDWDtBQUFBLFFBQ0o7QUFBQSxNQUNKO0FBQUEsSUFDSjtBQUFBLElBQ0EsdUJBQXVCO0FBQUE7QUFBQSxFQUUzQjtBQUFBLEVBQ0EsS0FBSztBQUFBLElBQ0QsY0FBYztBQUFBO0FBQUEsRUFDbEI7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
