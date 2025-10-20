import { useEffect, useRef, useState } from "react";
import {
	Calendar,
	Share2,
	Bookmark,
	Edit,
	Tag,
	Hash,
	ThumbsUp,
	ThumbsDown,
	Check,
} from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Button";
import { Badge } from "@/Components/Ui/Badge";
import { Separator } from "@/Components/Ui/Separator";
import { UserProfileHover } from "@/Components/Ui/UserProfileHover";
import { Link } from "@inertiajs/react";
import ApiService from "@/Services/ApiService";
import MarkdownRenderer from "@/Components/Ui/MarkdownRenderer";
import Feed from "@/Components/Layout/Feed";
import { useAuthStore } from "@/Stores/AuthStore";
import { Card, CardContent } from "@/Components/Ui/Card";
import { useTranslation } from "react-i18next";
import { toast } from "@/Hooks/useToast";
import TableOfContents from "@/Components/Ui/TableOfContents";
import { Comments } from "@/Components/Content/Comments";

export default function ArticlePage({ article }) {
	const [isLiked, setIsLiked] = useState(article.isLiked);
	const [isDisliked, setIsDisliked] = useState(article.isDisliked);
	const [likes, setLikes] = useState(Number(article.likes));
	const [dislikes, setDislikes] = useState(Number(article.dislikes));
	const [isSaved, setIsSaved] = useState(article.isSaved);

	const [isCopied, setIsCopied] = useState(false);
	const [isFeedVisible, setIsFeedVisible] = useState(false);
	const [isFeedLoading, setIsFeedLoading] = useState(false);
	const [isCommentsVisible, setIsCommentsVisible] = useState(false);
	const feedTriggerRef = useRef(null);
	const commentsTriggerRef = useRef(null);
	const authStore = useAuthStore();
	const { t } = useTranslation();

	const handleLike = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.like", { type: "article", slug: article.slug }))
			.then(() => {
				if (isLiked) {
					setIsLiked(false);
					setLikes(likes - 1);
				} else {
					if (isDisliked) {
						setIsDisliked(false);
						setDislikes(dislikes - 1);
					}
					setIsLiked(true);
					setLikes(likes + 1);
				}
			})
			.catch(() => {
				toast({
					title: t("Error"),
					description: t("You can react to the same article once a day"),
					variant: "destructive",
				});
			});
	};

	const handleDislike = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.dislike", { type: "article", slug: article.slug }))
			.then(() => {
				if (isDisliked) {
					setIsDisliked(false);
					setDislikes(dislikes - 1);
				} else {
					if (isLiked) {
						setIsLiked(false);
						setLikes(likes - 1);
					}
					setIsDisliked(true);
					setDislikes(dislikes + 1);
				}
			})
			.catch(() => {
				toast({
					title: t("Error"),
					description: t("You can react to the same article once a day"),
					variant: "destructive",
				});
			});
	};

	const handleSave = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.save", { type: "article", slug: article.slug })).then(
			() => {
				setIsSaved(!isSaved);
			},
		);
	};

	const handleShare = () => {
		if (navigator.share) {
			navigator.share({
				url: route("article.view", { slug: article.slug }),
			});
		} else {
			navigator.clipboard.writeText(route("article.view", { slug: article.slug }));
			setIsCopied(true);
			setTimeout(() => {
				setIsCopied(false);
			}, 1500);
		}
	};

	useEffect(() => {
		if (!isCommentsVisible) return;

		const observer = new IntersectionObserver(
			([entry]) => {
				if (entry.isIntersecting) {
					setIsFeedVisible(true);
					setIsFeedLoading(false);
					observer.disconnect();
				}
			},
			{
				rootMargin: "50px",
			},
		);

		if (feedTriggerRef.current) {
			setIsFeedLoading(true);
			observer.observe(feedTriggerRef.current);
		}

		return () => {
			observer.disconnect();
		};
	}, [isCommentsVisible]);

	useEffect(() => {
		const observer = new IntersectionObserver(
			([intersect]) => {
				if (intersect.isIntersecting) {
					setIsCommentsVisible(true);
				}
			},
			{
				rootMargin: "50px",
			},
		);

		if (commentsTriggerRef.current) {
			observer.observe(commentsTriggerRef.current);
		}

		return () => {
			observer.disconnect();
		};
	}, []);

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="right-6 top-24 fixed hidden lg:block z-100">
				<TableOfContents markdown={article.content} />
			</div>
			<div className="max-w-4xl mx-auto">
				<div className="mb-8">
					<div className="flex items-center space-x-2 text-sm text-muted-foreground mb-4">
						{article.categories.slice(0, 3).map((category) => (
							<Link
								key={category.slug}
								href={route("category.view", { slug: category.slug })}
							>
								<Badge
									key={category.slug}
									variant="outline"
									className="text-xs p-1 px-2"
								>
									<Tag className="h-4 w-4 mr-1" />
									{category.name}
								</Badge>
							</Link>
						))}
						{article.tags.slice(0, 3).map((tag) => (
							<Link key={tag.slug} href={route("tag.view", { slug: tag.slug })}>
								<Badge variant="outline" className="text-xs p-1 px-2">
									<Hash className="h-4 w-4 mr-1" />
									{tag.name}
								</Badge>
							</Link>
						))}
					</div>

					<h1 className="text-4xl font-bold mb-6">{article.title}</h1>

					<div className="flex items-center sm:justify-between flex-col sm:flex-row gap-3">
						<div className="flex items-center space-x-4">
							<UserProfileHover username={article.user.username}>
								<div className="flex items-center space-x-3 cursor-pointer">
									<Avatar className="h-12 w-12">
										<AvatarImage
											src={article.user.avatar}
											alt={article.user.name}
										/>
										<AvatarFallback>
											{article.user.name
												.split(" ")
												.map((n) => n[0])
												.join("")}
										</AvatarFallback>
									</Avatar>
									<div>
										<p className="font-medium">{article.user.name}</p>
										<p className="text-sm text-muted-foreground">
											@{article.user.username}
										</p>
									</div>
								</div>
							</UserProfileHover>

							<div className="flex items-center space-x-4 text-sm text-muted-foreground">
								<div className="flex items-center space-x-1">
									<Calendar className="h-4 w-4" />
									<span>
										{new Date(article.published_at).toLocaleDateString()}
									</span>
								</div>
							</div>
						</div>

						<div className="flex items-center space-x-2">
							{article.canUpdate && (
								<Button variant="outline" asChild size="sm">
									<Link
										className="flex"
										href={route("article.crud.edit.view", {
											article: article.slug,
										})}
									>
										<Edit className="h-4 w-4" />
									</Link>
								</Button>
							)}
							<Button
								variant={isLiked ? "default" : "outline"}
								size="sm"
								disabled={!authStore.isAuthenticated}
								onClick={handleLike}
							>
								<ThumbsUp
									className={`h-4 w-4 mr-1 ${isLiked ? "fill-current" : ""}`}
								/>
								{likes}
							</Button>
							<Button
								variant={isDisliked ? "default" : "outline"}
								size="sm"
								disabled={!authStore.isAuthenticated}
								onClick={handleDislike}
							>
								<ThumbsDown
									className={`h-4 w-4 mr-1 ${isDisliked ? "fill-current" : ""}`}
								/>
								{dislikes}
							</Button>
							<Button
								variant={isSaved ? "default" : "outline"}
								size="sm"
								disabled={!authStore.isAuthenticated}
								onClick={handleSave}
							>
								<Bookmark className={`h-4 w-4 ${isSaved ? "fill-current" : ""}`} />
							</Button>
							<Button variant="outline" size="sm" onClick={handleShare}>
								{isCopied ? (
									<Check className="h-4 w-4" />
								) : (
									<Share2 className="h-4 w-4" />
								)}
							</Button>
						</div>
					</div>
				</div>

				<Separator className="mb-8" />

				<MarkdownRenderer content={article.content} />

				{article.sources && article.sources.length > 0 && (
					<div className="mt-8">
						<h3 className="text-xl font-semibold mb-4 flex items-center">
							<Tag className="h-5 w-5 mr-2" />
							{t("Sources")}
						</h3>

						<Card>
							<CardContent className="p-4 space-y-4">
								{article.sources.map((source, index) => (
									<div
										key={index}
										className="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b last:border-0 pb-3 last:pb-0"
									>
										<div className="flex items-center space-x-3">
											<Bookmark className="h-5 w-5 text-muted-foreground" />
											<a
												href={source.url}
												target="_blank"
												rel="noopener noreferrer"
												className="text-primary hover:underline break-all"
											>
												{source.url}
											</a>
										</div>
										<div className="flex items-center text-sm text-muted-foreground mt-2 sm:mt-0">
											<Calendar className="h-4 w-4 mr-1" />
											{new Date(source.date).toLocaleDateString()}
										</div>
									</div>
								))}
							</CardContent>
						</Card>
					</div>
				)}

				{(!article.sources || article.sources.length == 0) && (
					<Separator className="mb-4 mt-8" />
				)}

				<div ref={commentsTriggerRef} className="h-10"></div>

				{isCommentsVisible && (
					<Comments
						commentsCounts={article.comments}
						type="article"
						slug={article.slug}
					/>
				)}

				{isFeedLoading && (
					<div className="flex justify-center py-8">
						<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
					</div>
				)}

				<div ref={feedTriggerRef} className="h-10"></div>
			</div>
			{isFeedVisible && <Feed hasNavigation hasSidebar filters={{ article: article.slug }} />}
		</div>
	);
}
