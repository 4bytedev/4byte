import Feed from "@/Components/Content/Feed";
import { useTranslation } from "react-i18next";

export default function SearchPage({ q, results }) {
	const { t } = useTranslation();
	console.log(q, results);

	return (
		<div className="max-w-2xl mx-auto">
			<Feed
				title={t("Search")}
				description={t("search_results", { q })}
				tabs={[{ label: t("All"), value: "all" }]}
				endpoint={route("api.search", { q })}
			/>
		</div>
	);
}
