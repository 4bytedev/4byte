import ApiService from "@/Services/ApiService";

export default {
	search: (query) => {
		return ApiService.fetchJson(
			route("api.search") + "?q=" + encodeURIComponent(query),
			{},
			{ method: "GET" },
		);
	},

	feedData: () => {
		return ApiService.fetchJson(route("api.feed.data"), {}, { method: "GET" });
	},

	createEntry: (data) => {
		return ApiService.fetchJson(route("api.entry.crud.create"), data, {
			isMultipart: true,
		});
	},
	createArticle: (data) => {
		return ApiService.fetchJson(route("api.article.crud.create"), data, {
			isMultipart: true,
		});
	},
	editArticle: (slug, data) => {
		return ApiService.fetchJson(route("api.article.crud.edit", { slug }), data, {
			isMultipart: true,
		});
	},
};
