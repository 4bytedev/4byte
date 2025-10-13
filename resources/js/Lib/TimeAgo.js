import { useTranslation } from "react-i18next";

export function useTimeAgo() {
	const { t } = useTranslation();

	return (dateString) => {
		const date = new Date(dateString);
		let seconds = Math.floor((new Date() - date) / 1000);

		const units = [
			{ name: "yearsAgo", seconds: 31536000 },
			{ name: "monthsAgo", seconds: 2592000 },
			{ name: "daysAgo", seconds: 86400 },
			{ name: "hoursAgo", seconds: 3600 },
			{ name: "minutesAgo", seconds: 60 },
			{ name: "secondsAgo", seconds: 1 },
		];

		const result = [];

		for (let i = 0; i < units.length; i++) {
			const interval = Math.floor(seconds / units[i].seconds);
			if (interval > 0) {
				result.push(t(units[i].name, { count: interval }));
				seconds -= interval * units[i].seconds;
			}
			if (result.length === 2) break;
		}

		if (result.length === 0) return t("justNow");

		return result.join(", ") + " " + t("ago");
	};
}
