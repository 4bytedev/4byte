import React, { useContext, useEffect, useState, useCallback, createContext } from "react";
import i18n from "@/i18n";

const I18nContext = createContext({ language: i18n.language, changeLanguage: () => {} });

export function I18nProvider({ children }) {
	const [language, setLanguage] = useState(i18n.language);

	const changeLanguage = useCallback((lng) => {
		i18n.changeLanguage(lng);
		setLanguage(lng);
		if (typeof window !== "undefined") {
			localStorage.setItem("language", lng);
			document.documentElement.setAttribute("lang", lng);
		}
	}, []);

	useEffect(() => {
		const onChange = (lng) => setLanguage(lng);
		i18n.on("languageChanged", onChange);
		return () => {
			i18n.off("languageChanged", onChange);
		};
	}, []);

	return (
		<I18nContext.Provider value={{ language, changeLanguage }}>{children}</I18nContext.Provider>
	);
}

export const useI18n = () => useContext(I18nContext);
