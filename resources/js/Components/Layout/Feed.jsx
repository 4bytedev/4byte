import { Sidebar } from "@/Components/Layout/Sidebar";
import { Navigation } from "@/Components/Layout/Navigation";
import ContentScroll from "@/Components/Content/ContentScroll";
import { useTranslation } from "react-i18next";
import { useSidebar } from "@/Contexts/SidebarContext";
import { useEffect } from "react";
import { SidebarOverlay, SidebarRoot } from "../Ui/Sidebar";

export default function Feed({ hasNavigation = false, hasSidebar = false, ...props }) {
	const { t } = useTranslation();
	const { setIsVisible, isOpen } = useSidebar();

	useEffect(() => {
		setIsVisible(hasNavigation);
		return () => setIsVisible(false);
	}, []);

	return (
		<div className="flex min-h-screen bg-background max-w-7xl mx-auto">
			{(hasNavigation || isOpen) && (
				<>
					<SidebarOverlay />
					<SidebarRoot>
						<div className="lg:w-72 md:w-48 sm:w-32 w-64 w-full sticky sm:top-16 h-[calc(100vh-4rem)]">
							<Navigation />
						</div>
					</SidebarRoot>
				</>
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
