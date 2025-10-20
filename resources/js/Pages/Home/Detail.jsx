import Feed from "@/Components/Layout/Feed";
import { useAuthStore } from "@/Stores/AuthStore";
import { useTranslation } from "react-i18next";

export default function HomePage() {
	const { t } = useTranslation();
	const authStore = useAuthStore();

	return (
		<Feed
			hasNavigation
			hasSidebar
			title={t("Discover")}
			description={t(
				"Stay updated with the latest articles, events, tutorials, and developer news",
			)}
			creator={authStore.isAuthenticated}
		/>
	);
}
