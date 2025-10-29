import { Sidebar } from "@/Components/Layout/Sidebar";
import { Navigation } from "@/Components/Layout/Navigation";
import ContentScroll from "@/Components/Content/ContentScroll";
import { useTranslation } from "react-i18next";

export default function Feed({ hasNavigation = false, hasSidebar = false, ...props }) {
	const { t } = useTranslation();

	return (
		<div className="flex min-h-screen bg-background max-w-7xl mx-auto">
			{hasNavigation && (
				<div className="lg:w-72 md:w-48 w-32 sticky top-16 h-[calc(100vh-4rem)] border-r border-border hidden sm:block">
					<Navigation />
				</div>
			)}

			<ContentScroll
				endpoint={route("api.feed.feed")}
				tabs={[
					{ label: t("All"), value: "all" },
					{ label: t("Articles"), value: "article" },
					{ label: t("News"), value: "news" },
				]}
				{...props}
			/>

			{hasSidebar && (
				<div className="w-72 sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto border-l border-border scrollbar-none hidden lg:block">
					<Sidebar />
				</div>
			)}
		</div>
	);
}
