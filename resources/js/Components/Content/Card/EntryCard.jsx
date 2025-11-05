import { Card, CardHeader, CardContent, CardFooter } from "@/Components/Ui/Card";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";
import { ImageSlider } from "../../Common/ImageSlider";
import MarkdownRenderer from "../../Common/MarkdownRenderer";
import { useAuthStore } from "@/Stores/AuthStore";
import { useState } from "react";
import { Button } from "../../Ui/Form/Button";
import {
	Bookmark,
	Calendar,
	Check,
	MessageCircle,
	Share2,
	ThumbsDown,
	ThumbsUp,
	Trash,
} from "lucide-react";
import ApiService from "@/Services/ApiService";
import { useTranslation } from "react-i18next";
import { toast } from "@/Hooks/useToast";
import { Link } from "@inertiajs/react";

export function EntryCard({
	user,
	content,
	media = [],
	slug,
	isLiked: initialIsLiked,
	isDisliked: initialIsDisliked,
	isSaved: initialIsSaved,
	likes: initialLikes,
	dislikes: initialDislikes,
	comments,
	canDelete,
	published_at,
}) {
	const authStore = useAuthStore();
	const [isLiked, setIsLiked] = useState(initialIsLiked);
	const [isDisliked, setIsDisliked] = useState(initialIsDisliked);
	const [isSaved, setIsSaved] = useState(initialIsSaved);
	const [isCopied, setIsCopied] = useState(false);
	const [likes, setLikes] = useState(initialLikes);
	const [dislikes, setDislikes] = useState(initialDislikes);
	const { t } = useTranslation();
	const hasMedia = media && media.length > 0;

	const handleLike = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.like", { type: "entry", slug: slug }))
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
					description: t("You can react to the same entry once a day"),
					variant: "destructive",
				});
			});
	};

	const handleDislike = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.dislike", { type: "entry", slug: slug }))
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
					description: t("You can react to the same entry once a day"),
					variant: "destructive",
				});
			});
	};

	const handleSave = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.save", { type: "entry", slug: slug })).then(() => {
			setIsSaved(!isSaved);
		});
	};

	const handleShare = () => {
		if (navigator.share) {
			navigator.share({
				url: route("entry.view", { slug: slug }),
			});
		} else {
			navigator.clipboard.writeText(route("entry.view", { slug: slug }));
			setIsCopied(true);
			setTimeout(() => {
				setIsCopied(false);
			}, 1500);
		}
	};

	return (
		<Card className="group hover:shadow-lg transition-all duration-200 overflow-hidden">
			<CardHeader className="p-2">
				<div className="flex items-center justify-between">
					<UserProfileHover username={user.username}>
						<div className="flex items-center space-x-2">
							<div className="px-2 py-1.5 text-sm flex">
								<Avatar className="h-10 w-10 me-2">
									<AvatarImage
										src={user.avatar || "/placeholder-avatar.jpg"}
										alt={user.name || "User"}
									/>
									<AvatarFallback>
										{user.name
											.split(" ")
											.map((n) => n[0])
											.join("") || "U"}
									</AvatarFallback>
								</Avatar>
								<div>
									<div className="font-medium flex">
										{user.name}
										<span className="flex text-muted-foreground items-center ml-2">
											<Calendar className="h-3 w-3" />
											<span>
												{new Date(published_at).toLocaleDateString()}
											</span>
										</span>
									</div>
									<div className="text-muted-foreground">@{user.username}</div>
								</div>
							</div>
						</div>
					</UserProfileHover>
				</div>
			</CardHeader>

			{content && (
				<CardContent className="px-4 py-0">
					<div className="text-sm text-foreground whitespace-pre-line leading-relaxed">
						<MarkdownRenderer content={content} />
					</div>
				</CardContent>
			)}

			{hasMedia && (
				<div className="relative w-full bg-background overflow-hidden">
					<ImageSlider
						medias={media}
						spaceBetween={0}
						slidesPerView={1}
						className="w-full h-auto max-h-64 aspect-[4/5] md:aspect-[1/1] object-cover object-center transition-transform duration-1000 group-hover:scale-105"
					/>
				</div>
			)}

			<CardFooter className="p-2">
				<Button
					variant="ghost"
					size="sm"
					disabled={!authStore.isAuthenticated}
					onClick={handleLike}
				>
					<ThumbsUp className={`h-4 w-4 mr-1 ${isLiked ? "fill-current" : ""}`} />
					{likes}
				</Button>
				<Button
					variant="ghost"
					size="sm"
					disabled={!authStore.isAuthenticated}
					onClick={handleDislike}
				>
					<ThumbsDown className={`h-4 w-4 mr-1 ${isDisliked ? "fill-current" : ""}`} />
					{dislikes}
				</Button>
				<Button variant="ghost" size="sm">
					<Link href={route("entry.view", { slug: slug })} className="flex items-center">
						<MessageCircle className="h-4 w-4 mr-1" />
						{comments}
					</Link>
				</Button>
				<Button
					variant="ghost"
					size="sm"
					disabled={!authStore.isAuthenticated}
					onClick={handleSave}
				>
					<Bookmark className={`h-4 w-4 ${isSaved ? "fill-current" : ""}`} />
				</Button>
				<Button variant="ghost" size="sm" onClick={handleShare}>
					{isCopied ? <Check className="h-4 w-4" /> : <Share2 className="h-4 w-4" />}
				</Button>
				<div className="ml-auto">
					{canDelete && (
						<Button variant="ghost" size="sm">
							<Trash className="h-4 w-4" />
						</Button>
					)}
				</div>
			</CardFooter>
		</Card>
	);
}
