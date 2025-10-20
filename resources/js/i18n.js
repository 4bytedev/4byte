import i18n from "i18next";
import { initReactI18next } from "react-i18next";
import en from "@/Lang/en.json";
import tr from "@/Lang/tr.json";

const resources = {
	en: { translation: en },
	tr: { translation: tr },
};

function getInitialLng() {
	if (typeof window === "undefined") return "tr";
	const saved = localStorage.getItem("language");
	const htmlLang = document.documentElement.getAttribute("lang");
	return saved || htmlLang || "tr";
}

if (!i18n.isInitialized) {
	i18n.use(initReactI18next).init({
		resources,
		lng: getInitialLng(),
		fallbackLng: "tr",
		interpolation: { escapeValue: false },
	});
}

export default i18n;
