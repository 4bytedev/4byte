import React from "react";
import { createRoot } from "react-dom/client";
import Layout from "./Components/Layout/Layout";
import { createInertiaApp } from "@inertiajs/react";

createInertiaApp({
	resolve: (name) => {
		const pages = import.meta.glob("./Pages/**/*.jsx", { eager: true });
		let page = pages[`./Pages/${name}.jsx`].default;

		page.layout ??= (page) => <Layout>{page}</Layout>;

		return page;
	},
	setup({ el, App, props }) {
		const root = createRoot(el);
		root.render(<App {...props} />);
	},
	progress: {
		color: "#4B5563",
	},
});
