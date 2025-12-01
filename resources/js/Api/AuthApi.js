import ApiService from "@/Services/ApiService";

export default {
	login: (data) => {
		return ApiService.fetchJson(route("api.auth.login"), data);
	},
	register: (data) => {
		return ApiService.fetchJson(route("api.auth.register"), data);
	},
	forgotPassword: (data) => {
		return ApiService.fetchJson(route("api.auth.forgot-password"), data);
	},
	resetPassword: (data) => {
		return ApiService.fetchJson(route("api.auth.reset-password.request"), data);
	},
	logout: () => {
		return ApiService.fetchJson(route("api.auth.logout"));
	},
};
