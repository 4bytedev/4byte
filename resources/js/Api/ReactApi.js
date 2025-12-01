import ApiService from "@/Services/ApiService";

export default {
	save: (data) => {
		return ApiService.fetchJson(route("api.react.save", data));
	},
	follow: (data) => {
		return ApiService.fetchJson(route("api.react.follow", data));
	},
	like: (data) => {
		return ApiService.fetchJson(route("api.react.like", data));
	},
	dislike: (data) => {
		return ApiService.fetchJson(route("api.react.dislike", data));
	},
	comments: (data) => {
		return ApiService.fetchJson(route("api.react.comments", data));
	},
	replies: (data) => {
		return ApiService.fetchJson(route("api.react.comment.replies", data));
	},
	submitComment: (query, body) => {
		return ApiService.fetchJson(route("api.react.comment", query), body);
	},
};
