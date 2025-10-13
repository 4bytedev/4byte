import { createInertiaApp } from "@inertiajs/react";
import createServer from "@inertiajs/react/server";
import ReactDOMServer from "react-dom/server";
import Layout from "./Components/Layout/Layout";

createServer((page) =>
	createInertiaApp({
		page,
		render: ReactDOMServer.renderToString,
		resolve: (name) => {
			const pages = import.meta.glob("./Pages/**/*.jsx", { eager: true });
			let page = pages[`./Pages/${name}.jsx`].default;

			page.layout ??= (page) => <Layout>{page}</Layout>;

			return page;
		},
		setup: ({ App, props }) => <App {...props} />,
	}),
);
