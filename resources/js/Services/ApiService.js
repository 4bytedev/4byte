export default class ApiService {
	static csrfToken = null;

	static async fetchJson(url, body = {}, options = {}) {
		const headers = {
			"Content-Type": "application/json",
			Accept: "application/json",
			"X-CSRF-TOKEN": this.csrfToken,
			...options.headers,
		};
		options.withCredentials = true;
		options.redirect = "manual";
		if (!options.method) {
			options.method = "POST";
		}

		if (options.isMultipart) {
			const formData = new FormData();
			for (const key in body) {
				formData.append(key, body[key]);
			}
			options.body = formData;
			delete headers["Content-Type"];
		} else if (options.method && options.method !== "GET") {
			headers["Content-Type"] = "application/json";
			options.body = JSON.stringify(body);
		}

		const response = await fetch(url, { ...options, headers });

		const data = await response.json().catch(() => null);

		if (!response.ok) {
			return Promise.reject(data);
		}
		return Promise.resolve(data);
	}

	static setToken(csrfToken) {
		this.csrfToken = csrfToken;
	}
}
