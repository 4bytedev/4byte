import ApiService from "@/Services/ApiService";

export default {
	preview: (data) => {
		return ApiService.fetchJson(route("api.user.preview", data), {}, { method: "GET" });
	},

	getNotifications: () => {
		return ApiService.fetchJson(
			route("api.notification.list"),
			{},
			{
				method: "GET",
			},
		);
	},
	readNotification: (data) => {
		return ApiService.fetchJson(route("api.notification.read"), data);
	},
	readNotifications: () => {
		return ApiService.fetchJson(route("api.notification.read-all"));
	},
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
	resendVerify: () => {
		return ApiService.fetchJson(route("api.user.verification.resend"));
	},
};
