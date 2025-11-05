import { useEffect, useState } from "react";
import { ChevronDown, ChevronRight, BookOpen, TrendingUp, ChartNoAxesCombined } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@/Components/Ui/Collapsible";
import { useTranslation } from "react-i18next";
import { ContentPreviewCard } from "../Content/ContentPreviewCard";
import ApiService from "@/Services/ApiService";

export function Sidebar() {
	const [articles, setArticles] = useState();
	const [tags, setTags] = useState();
	const [categories, setCategories] = useState();
	const [isLoading, setIsLoading] = useState(true);
	const [isTagsOpen, setIsTagsOpen] = useState(true);
	const [isArticlesOpen, setIsArticlesOpen] = useState(true);
	const [isCategoriesOpen, setIsCategoriesOpen] = useState(true);
	const { t } = useTranslation();

	useEffect(() => {
		setIsLoading(true);
		ApiService.fetchJson(route("api.feed.data"), {}, { method: "GET" }).then((response) => {
			setArticles(response.articles);
			setTags(response.tags);
			setCategories(response.categories);
			setIsLoading(false);
		});
	}, []);

	if (isLoading) {
		return (
			<div className="flex justify-center py-8">
				<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
			</div>
		);
	}

	return (
		<aside className="w-64 border-l bg-background/50 p-4 space-y-6">
			<Collapsible open={isTagsOpen} onOpenChange={setIsTagsOpen}>
				<CollapsibleTrigger asChild>
					<Button variant="ghost" className="w-full justify-between p-0 h-auto">
						<div className="flex items-center m-2 space-x-2">
							<TrendingUp className="h-4 w-4" />
							<span className="font-medium">{t("Trending Tags")}</span>
						</div>
						{isTagsOpen ? (
							<ChevronDown className="h-4 w-4" />
						) : (
							<ChevronRight className="h-4 w-4" />
						)}
					</Button>
				</CollapsibleTrigger>
				<CollapsibleContent className="space-y-2 mt-3">
					{tags.map((tag) => (
						<ContentPreviewCard
							key={tag.data.slug}
							item={{ ...tag.data, total: tag.total }}
						/>
					))}
				</CollapsibleContent>
			</Collapsible>

			<Collapsible open={isCategoriesOpen} onOpenChange={setIsCategoriesOpen}>
				<CollapsibleTrigger asChild>
					<Button variant="ghost" className="w-full justify-between p-0 h-auto">
						<div className="flex items-center m-2 space-x-2">
							<ChartNoAxesCombined className="h-4 w-4" />
							<span className="font-medium">{t("Trending Categories")}</span>
						</div>
						{isCategoriesOpen ? (
							<ChevronDown className="h-4 w-4" />
						) : (
							<ChevronRight className="h-4 w-4" />
						)}
					</Button>
				</CollapsibleTrigger>
				<CollapsibleContent className="space-y-2 mt-3">
					{categories.map((category) => (
						<ContentPreviewCard
							key={category.data.slug}
							item={{ ...category.data, total: category.total }}
						/>
					))}
				</CollapsibleContent>
			</Collapsible>

			<Collapsible open={isArticlesOpen} onOpenChange={setIsArticlesOpen}>
				<CollapsibleTrigger asChild>
					<Button variant="ghost" className="w-full justify-between p-0 h-auto">
						<div className="flex items-center m-2 space-x-2">
							<BookOpen className="h-4 w-4" />
							<span className="font-medium">{t("Trending Articles")}</span>
						</div>
						{isArticlesOpen ? (
							<ChevronDown className="h-4 w-4" />
						) : (
							<ChevronRight className="h-4 w-4" />
						)}
					</Button>
				</CollapsibleTrigger>
				<CollapsibleContent className="space-y-2 mt-3">
					{articles.map((article) => (
						<ContentPreviewCard key={article.slug} item={article} />
					))}
				</CollapsibleContent>
			</Collapsible>
		</aside>
	);
}
