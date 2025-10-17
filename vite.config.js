import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import path from "path";

export default ({ mode }) => {
	process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

	return defineConfig({
		build: {
			rollupOptions: {
				output: {
					manualChunks: {
						"react-vendor": ["react", "react-dom"],
						"inertia-vendor": ["@inertiajs/react"],
						"radix-vendor": ["radix-ui", "@radix-ui/react-accordion"],
					},
				},
			},
		},
		plugins: [
			laravel({
				input: ["resources/js/app.jsx", "resources/css/app.css"],
				ssr: "resources/js/ssr.jsx",
				refresh: true,
			}),
			react(),
			// visualizer()
		],
		resolve: {
			alias: {
				"ziggy-js": path.resolve("vendor/tightenco/ziggy"),
			},
		},
		base: process.env.VITE_BASE_URL ? process.env.VITE_BASE_URL + "/build/" : "/build/",
	});
};
