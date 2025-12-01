import { useEffect, useRef, useState } from "react";
import { Calendar, Share2, Bookmark, ThumbsUp, ThumbsDown, Check } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Form/Button";
import { Separator } from "@/Components/Ui/Separator";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";
import MarkdownRenderer from "@/Components/Common/MarkdownRenderer";
import Feed from "@/Components/Content/Feed";
import { useAuthStore } from "@/Stores/AuthStore";
import { toast } from "@/Hooks/useToast";
import { ImageSlider } from "@/Components/Common/ImageSlider";
import { Comments } from "@/Components/Content/Comments";
import { useTranslation } from "react-i18next";
import ReactApi from "@/Api/ReactApi";
import { useMutation } from "@tanstack/react-query";

export default function EntryPage({ entry }) {
	const [isLiked, setIsLiked] = useState(entry.isLiked);
	const [isDisliked, setIsDisliked] = useState(entry.isDisliked);
	const [likes, setLikes] = useState(Number(entry.likes));
	const [dislikes, setDislikes] = useState(Number(entry.dislikes));
	const [isSaved, setIsSaved] = useState(entry.isSaved);

	const [isCopied, setIsCopied] = useState(false);
	const [isFeedVisible, setIsFeedVisible] = useState(false);
	const [isFeedLoading, setIsFeedLoading] = useState(false);
	const [isCommentsVisible, setIsCommentsVisible] = useState(false);
	const feedTriggerRef = useRef(null);
	const commentsTriggerRef = useRef(null);
	const authStore = useAuthStore();
	const { t } = useTranslation();
	const hasMedia = entry.media && entry.media.length > 0;

	const likeMutation = useMutation({
		mutationFn: () => ReactApi.like({ type: "entry", slug: entry.slug }),
		onSuccess: () => {
			setIsLiked((prev) => {
				if (prev) {
					setLikes((l) => l - 1);
					return false;
				}

				if (isDisliked) {
					setIsDisliked(false);
					setDislikes((d) => d - 1);
				}

				setLikes((l) => l + 1);
				return true;
			});
		},
		onError: () => {
			toast({
				title: t("Error"),
				description: t("You can react to the same entry once a day"),
				variant: "destructive",
			});
		},
	});

	const handleLike = () => {
		if (!authStore.isAuthenticated) return;
		likeMutation.mutate();
	};

	const dislikeMutation = useMutation({
		mutationFn: () => ReactApi.dislike({ type: "entry", slug: entry.slug }),
		onSuccess: () => {
			setIsDisliked((disliked) => {
				const willDislike = !disliked;

				if (willDislike) {
					if (isLiked) {
						setIsLiked(false);
						setLikes((l) => l - 1);
					}

					setDislikes((d) => d + 1);
				} else {
					setDislikes((d) => d - 1);
				}

				return willDislike;
			});
		},
		onError: () => {
			toast({
				title: t("Error"),
				description: t("You can react to the same entry once a day"),
				variant: "destructive",
			});
		},
	});

	const handleDislike = () => {
		if (!authStore.isAuthenticated) return;
		dislikeMutation.mutate();
	};

	const saveMutation = useMutation({
		mutationFn: () => ReactApi.save({ type: "entry", slug: entry.slug }),
		onSuccess: () => {
			setIsSaved(!isSaved);
		},
	});

	const handleSave = () => {
		if (!authStore.isAuthenticated) return;
		saveMutation.mutate();
	};

	const handleShare = () => {
		if (navigator.share) {
			navigator.share({
				url: route("entry.view", { slug: entry.slug }),
			});
		} else {
			navigator.clipboard.writeText(route("entry.view", { slug: entry.slug }));
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
			<div className="max-w-4xl mx-auto">
				<div className="mb-8">
					<h1 className="text-4xl font-bold mb-6">{entry.title}</h1>

					<div className="flex items-center sm:justify-between flex-col sm:flex-row gap-3">
						<div className="flex items-center space-x-4">
							<UserProfileHover username={entry.user.username}>
								<div className="flex items-center space-x-3 cursor-pointer">
									<Avatar className="h-12 w-12">
										<AvatarImage
											src={entry.user.avatar}
											alt={entry.user.name}
										/>
										<AvatarFallback>
											{entry.user.name
												.split(" ")
												.map((n) => n[0])
												.join("")}
										</AvatarFallback>
									</Avatar>
									<div>
										<p className="font-medium">{entry.user.name}</p>
										<p className="text-sm text-muted-foreground">
											@{entry.user.username}
										</p>
									</div>
								</div>
							</UserProfileHover>

							<div className="flex items-center space-x-4 text-sm text-muted-foreground">
								<div className="flex items-center space-x-1">
									<Calendar className="h-4 w-4" />
									<span>{new Date(entry.published_at).toLocaleDateString()}</span>
								</div>
							</div>
						</div>

						<div className="flex items-center space-x-2">
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

				{entry.content && <MarkdownRenderer content={entry.content || ""} />}

				{hasMedia && (
					<div className="relative w-full bg-background overflow-hidden">
						<ImageSlider
							medias={entry.media}
							spaceBetween={0}
							slidesPerView={1}
							className="w-full h-auto aspect-[4/5] md:aspect-[1/1] object-cover object-center transition-transform duration-1000 group-hover:scale-105"
						/>
					</div>
				)}

				<div ref={commentsTriggerRef} className="h-10"></div>

				{isCommentsVisible && (
					<Comments commentsCounts={entry.comments} type="entry" slug={entry.slug} />
				)}

				{isFeedLoading && (
					<div className="flex justify-center py-8">
						<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
					</div>
				)}

				<div ref={feedTriggerRef} className="h-10"></div>
			</div>
			{isFeedVisible && <Feed hasNavigation hasSidebar filters={{ entry: entry.slug }} />}
		</div>
	);
}
