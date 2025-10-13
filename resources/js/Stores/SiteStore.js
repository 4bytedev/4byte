import { useTheme } from "@/Contexts/ThemeContext";
import { create } from "zustand";

export const useSiteStore = create()(
	(set, get) => ({
		// State
		title: null,
		logo: {
			dark: null,
			light: null,
		},
		favicon: null,
		pages: {
			terms: null,
			privacy: null,
		},
		settings: {
			verification: null,
			login: null,
			register: null,
		},
		i18n: {
			languages: [],
			default: null,
		},
		banners: [],

		setSite: (siteData) => {
			set({ ...siteData });
		},

		getLogo: () => {
			const { theme } = useTheme();
			return get().logo[theme];
		},

		getBanner: (render_location) => {
			return get().banners.filter((b) => b.data.render_location == render_location);
		},
	}),
	{
		name: "site-store",
		partialize: (state) => state,
	},
);
