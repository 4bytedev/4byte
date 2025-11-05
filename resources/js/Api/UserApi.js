import ApiService from "@/Services/ApiService";

export default {
	updateAccount: (data) => {
		return ApiService.fetchJson(route("api.user.settings.account"), data, {
			isMultipart: true,
		});
	},
	updateProfile: (data) => {
		return ApiService.fetchJson(route("api.user.settings.profile"), data, {
			isMultipart: true,
		});
	},
	changePassword: (data) => {
		return ApiService.fetchJson(route("api.user.settings.password"), data);
	},
	deleteAccount: (data) => {
		return ApiService.fetchJson(route("api.user.settings.delete-account"), data);
	},
	logOutOtherBrowserSessions: (data) => {
		return ApiService.fetchJson(route("api.user.settings.logout-other-sessions"), data);
	},
};
