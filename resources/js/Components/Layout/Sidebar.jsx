import { useState } from "react";
import {
	ChevronDown,
	ChevronRight,
	Hash,
	BookOpen,
	TrendingUp,
	Tag,
	ChartNoAxesCombined,
} from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Badge } from "@/Components/Ui/Badge";
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@/Components/Ui/Collapsible";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Link } from "@inertiajs/react";
import { UserProfileHover } from "@/Components/Ui/UserProfileHover";
import { useTranslation } from "react-i18next";

export function Sidebar({ tags, categories, articles }) {
	const [isTagsOpen, setIsTagsOpen] = useState(true);
	const [isArticlesOpen, setIsArticlesOpen] = useState(true);
	const [isCategoriesOpen, setIsCategoriesOpen] = useState(true);
	const { t } = useTranslation();

	return (
		<aside className="w-64 border-l bg-background/50 p-4 space-y-6">
			{/* Trending Tags */}
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
						<Link key={tag.name} href={route("tag.view", { slug: tag.slug })}>
							<div className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50 cursor-pointer transition-colors">
								<div className="flex items-center space-x-2">
									<Hash className="h-3 w-3 text-muted-foreground" />
									<span className="text-sm">{tag.name}</span>
								</div>
								<Badge variant="secondary" className="text-xs">
									{tag.total}
								</Badge>
							</div>
						</Link>
					))}
				</CollapsibleContent>
			</Collapsible>

			{/* Trending Tags */}
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
						<Link
							key={category.slug}
							href={route("category.view", { slug: category.slug })}
						>
							<div className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50 cursor-pointer transition-colors">
								<div className="flex items-center space-x-2">
									<Tag className="h-3 w-3 text-muted-foreground" />
									<span className="text-sm">{category.name}</span>
								</div>
								<Badge variant="secondary" className="text-xs">
									{category.total}
								</Badge>
							</div>
						</Link>
					))}
				</CollapsibleContent>
			</Collapsible>

			{/* Popular Articles */}
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
						<div
							key={article.slug}
							className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50 cursor-pointer transition-colors"
						>
							<div className="flex items-center space-x-2 w-full">
								<UserProfileHover username={article.user.username}>
									<Avatar className="h-8 w-8">
										<AvatarImage
											src={article.user.avatar || "/placeholder-avatar.jpg"}
											alt={article.user.name || "User"}
										/>
										<AvatarFallback>
											{article.user.name
												.split(" ")
												.map((n) => n[0])
												.join("") || "U"}
										</AvatarFallback>
									</Avatar>
								</UserProfileHover>
								<Link
									href={route("article.view", { slug: article.slug })}
									className="w-full"
								>
									<span className="text-xs">{article.title}</span>
								</Link>
							</div>
						</div>
					))}
				</CollapsibleContent>
			</Collapsible>
		</aside>
	);
}
