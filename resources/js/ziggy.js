const Ziggy = {
	url: "http:\/\/127.0.0.1:8000",
	port: 8000,
	defaults: {},
	routes: {
		"sanctum.csrf-cookie": { uri: "sanctum\/csrf-cookie", methods: ["GET", "HEAD"] },
		"livewire.update": { uri: "livewire\/update", methods: ["POST"] },
		"livewire.upload-file": { uri: "livewire\/upload-file", methods: ["POST"] },
		"livewire.preview-file": {
			uri: "livewire\/preview-file\/{filename}",
			methods: ["GET", "HEAD"],
			parameters: ["filename"],
		},
		"article.crud.create.view": { uri: "makale\/yaz", methods: ["GET", "HEAD"] },
		"article.crud.edit.view": {
			uri: "makale\/{article}\/duzenle",
			methods: ["GET", "HEAD"],
			parameters: ["article"],
			bindings: { article: "slug" },
		},
		"article.view": { uri: "makale\/{slug}", methods: ["GET", "HEAD"], parameters: ["slug"] },
		"api.article.crud.create": { uri: "api\/article\/crud\/create", methods: ["POST"] },
		"api.article.crud.edit": {
			uri: "api\/article\/crud\/{article}\/edit",
			methods: ["POST"],
			parameters: ["article"],
			bindings: { article: "slug" },
		},
		"category.view": {
			uri: "kategori\/{slug}",
			methods: ["GET", "HEAD"],
			parameters: ["slug"],
		},
		"page.view": { uri: "sayfa\/{slug}", methods: ["GET", "HEAD"], parameters: ["slug"] },
		"api.feed.data": { uri: "api\/feed", methods: ["GET", "HEAD"] },
		"api.feed.feed": { uri: "api\/feed\/feed", methods: ["GET", "HEAD"] },
		"tag.view": { uri: "etiket\/{slug}", methods: ["GET", "HEAD"], parameters: ["slug"] },
		"entry.view": { uri: "entry\/{slug}", methods: ["GET", "HEAD"], parameters: ["slug"] },
		"api.entry.crud.create": { uri: "api\/entry\/crud\/create", methods: ["POST"] },
		"api.react.like": {
			uri: "api\/react\/{type}\/{slug}\/like",
			methods: ["POST"],
			parameters: ["type", "slug"],
		},
		"api.react.dislike": {
			uri: "api\/react\/{type}\/{slug}\/dislike",
			methods: ["POST"],
			parameters: ["type", "slug"],
		},
		"api.react.save": {
			uri: "api\/react\/{type}\/{slug}\/save",
			methods: ["POST"],
			parameters: ["type", "slug"],
		},
		"api.react.comment": {
			uri: "api\/react\/{type}\/{slug}\/comment",
			methods: ["POST"],
			parameters: ["type", "slug"],
		},
		"api.react.follow": {
			uri: "api\/react\/{type}\/{slug}\/follow",
			methods: ["POST"],
			parameters: ["type", "slug"],
		},
		"api.react.comments": {
			uri: "api\/react\/{type}\/{slug}\/comments",
			methods: ["POST"],
			parameters: ["type", "slug"],
		},
		"api.react.comment.replies": {
			uri: "api\/react\/{type}\/{slug}\/comment\/{parent}\/replies",
			methods: ["POST"],
			parameters: ["type", "slug", "parent"],
		},
		"home.view": { uri: "\/", methods: ["GET", "HEAD"] },
		"user.view": { uri: "@{username}", methods: ["GET", "HEAD"], parameters: ["username"] },
		"user.settings.view": { uri: "user\/me\/settings", methods: ["GET", "HEAD"] },
		"user.verification.view": { uri: "user\/me\/verification", methods: ["GET", "HEAD"] },
		"user.verification.verify": {
			uri: "user\/me\/verification\/verify\/{id}\/{hash}",
			methods: ["GET", "HEAD"],
			parameters: ["id", "hash"],
		},
		"auth.reset-password.view": { uri: "auth\/reset-password", methods: ["GET", "HEAD"] },
		"api.user.preview": {
			uri: "api\/user\/{username}\/preview",
			methods: ["GET", "HEAD"],
			parameters: ["username"],
		},
		"api.auth.login": { uri: "api\/auth\/login", methods: ["POST"] },
		"api.auth.register": { uri: "api\/auth\/register", methods: ["POST"] },
		"api.auth.forgot-password": { uri: "api\/auth\/forgot-password", methods: ["POST"] },
		"api.auth.reset-password.request": { uri: "api\/auth\/reset-password", methods: ["POST"] },
		"api.auth.logout": { uri: "api\/auth\/logout", methods: ["POST"] },
		"api.user.settings.account": { uri: "api\/user\/me\/settings\/account", methods: ["POST"] },
		"api.user.settings.profile": { uri: "api\/user\/me\/settings\/profile", methods: ["POST"] },
		"api.user.settings.password": {
			uri: "api\/user\/me\/settings\/password",
			methods: ["POST"],
		},
		"api.user.settings.logout-other-sessions": {
			uri: "api\/user\/me\/settings\/logout-other-sessions",
			methods: ["POST"],
		},
		"api.user.settings.delete-account": {
			uri: "api\/user\/me\/settings\/delete-account",
			methods: ["POST"],
		},
		"api.user.verification.resend": {
			uri: "api\/user\/me\/verification\/resend",
			methods: ["POST"],
		},
		"api.notification.list": { uri: "api\/notifications", methods: ["GET", "HEAD"] },
		"api.notification.count": { uri: "api\/notifications\/count", methods: ["GET", "HEAD"] },
		"api.notification.mark-as-read": {
			uri: "api\/notifications\/mark-as-read",
			methods: ["POST"],
		},
		"api.notification.mark-all-as-read": {
			uri: "api\/notifications\/mark-all-as-read",
			methods: ["POST"],
		},
		"storage.local": {
			uri: "storage\/{path}",
			methods: ["GET", "HEAD"],
			wheres: { path: ".*" },
			parameters: ["path"],
		},
	},
};
if (typeof window !== "undefined" && typeof window.Ziggy !== "undefined") {
	Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
