import { useEffect, useRef, useState } from "react";
import {
	Calendar,
	Share2,
	Bookmark,
	ThumbsUp,
	ThumbsDown,
	Check,
	MessageCircle,
	Heart,
	ArrowDown,
	ArrowUp,
} from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Button";
import { Separator } from "@/Components/Ui/Separator";
import { UserProfileHover } from "@/Components/Ui/UserProfileHover";
import ApiService from "@/Services/ApiService";
import MarkdownRenderer from "@/Components/Ui/MarkdownRenderer";
import Feed from "@/Components/Layout/Feed";
import { useAuthStore } from "@/Stores/AuthStore";
import { Card, CardContent } from "@/Components/Ui/Card";
import { Textarea } from "@/Components/Ui/Textarea";
import { Trans, useTranslation } from "react-i18next";
import { useTimeAgo } from "@/Lib/TimeAgo";
import { toast } from "@/Hooks/useToast";
import { ImageSlider } from "@/Components/Ui/ImageSlider";

export default function EntryPage({ entry }) {
	const [isLiked, setIsLiked] = useState(entry.isLiked);
	const [isDisliked, setIsDisliked] = useState(entry.isDisliked);
	const [likes, setLikes] = useState(Number(entry.likes));
	const [dislikes, setDislikes] = useState(Number(entry.dislikes));
	const [isSaved, setIsSaved] = useState(entry.isSaved);

	const [commentsCount, setCommentsCount] = useState(Number(entry.comments));
	const [isCopied, setIsCopied] = useState(false);
	const [isFeedVisible, setIsFeedVisible] = useState(false);
	const [isFeedLoading, setIsFeedLoading] = useState(false);
	const [isCommentsVisible, setIsCommentsVisible] = useState(false);
	const [isCommentsLoading, setIsCommentsLoading] = useState(false);
	const [comment, setComment] = useState("");
	const [comments, setComments] = useState([]);
	const [showReplies, setShowReplies] = useState({});
	const [showRepliesTextarea, setshowRepliesTextarea] = useState({});
	const [isRepliesLoading, setIsRepliesLoading] = useState({});
	const [repliesComments, setRepliesComments] = useState({});
	const [replies, setReplies] = useState({});
	const feedTriggerRef = useRef(null);
	const commentsTriggerRef = useRef(null);
	const authStore = useAuthStore();
	const [errors, setErrors] = useState({});
	const { t } = useTranslation();
	const timeAgo = useTimeAgo();
	const hasMedia = entry.media && entry.media.length > 0;

	const handleLike = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.entry.like", { slug: entry.slug }))
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
		ApiService.fetchJson(route("api.entry.dislike", { slug: entry.slug }))
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
		ApiService.fetchJson(route("api.entry.save", { slug: entry.slug })).then(() => {
			setIsSaved(!isSaved);
		});
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

	const validateComment = () => {
		const newErrors = {};

		if (!comment) newErrors.comment = t("Comment is required");
		else if (comment.length < 20)
			newErrors.comment = t("Comment must be at least 20 characters");

		setErrors(newErrors);
		return Object.keys(newErrors).length === 0;
	};

	const validateReply = (commentId) => {
		const newErrors = {};

		if (!repliesComments[commentId]) newErrors[commentId] = t("Comment is required");
		else if (repliesComments[commentId].length < 20)
			newErrors[commentId] = t("Comment must be at least 20 characters");

		setErrors(newErrors);

		return Object.keys(newErrors).length === 0;
	};

	const handleComment = () => {
		setErrors({});

		if (!validateComment()) return;

		const data = {
			content: comment,
			parent: null,
		};

		ApiService.fetchJson(route("api.entry.comment", { slug: entry.slug }), data).then(
			(response) => {
				setComment("");
				setCommentsCount(commentsCount + 1);
				setComments([response, ...comments]);
			},
		);
	};

	const handleReply = (parentId) => {
		setErrors({});

		if (!parentId) return;
		if (!validateReply(parentId)) return;

		const data = {
			content: repliesComments[parentId],
			parent: parentId,
		};

		ApiService.fetchJson(route("api.entry.comment", { slug: entry.slug }), data).then(
			(response) => {
				setRepliesComments({ ...repliesComments, [parentId]: "" });
				setCommentsCount(commentsCount + 1);
				if (!parentId) {
					setComments([response, ...comments]);
				} else {
					setReplies({ ...replies, [parentId]: [response, ...replies[parentId]] });
					setComments((prev) =>
						prev.map((c) => (c.id === parentId ? { ...c, replies: c.replies + 1 } : c)),
					);
				}
			},
		);
	};

	const toggleReplyTextarea = (commentId) => {
		setshowRepliesTextarea({
			...showRepliesTextarea,
			[commentId]: !showRepliesTextarea[commentId],
		});
	};

	const toggleReplies = (commentId) => {
		if (showReplies[commentId]) {
			setShowReplies({ ...showReplies, [commentId]: false });
		} else {
			if (!replies[commentId]) {
				setIsRepliesLoading({ ...isRepliesLoading, [commentId]: true });
				ApiService.fetchJson(
					route("api.entry.comment.replies", {
						slug: entry.slug,
						comment: commentId,
					}),
				).then((response) => {
					setReplies({ ...replies, [commentId]: response });
					setShowReplies({ ...showReplies, [commentId]: true });
					setIsRepliesLoading({ ...isRepliesLoading, [commentId]: false });
				});
			} else {
				setShowReplies({ ...showReplies, [commentId]: true });
			}
		}
	};

	const handleCommentLike = (commentId, parentId) => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(
			route("api.entry.comment.like", { slug: entry.slug, comment: commentId }),
		)
			.then(() => {
				if (!parentId) {
					setComments((prev) =>
						prev.map((c) =>
							c.id === commentId
								? { ...c, likes: c.likes + (c.liked ? -1 : +1), liked: !c.liked }
								: c,
						),
					);
				} else {
					setReplies((prevReplies) => ({
						...prevReplies,
						[parentId]: prevReplies[parentId].map((reply) =>
							reply.id === commentId
								? {
										...reply,
										likes: reply.likes + (reply.liked ? -1 : 1),
										liked: !reply.liked,
									}
								: reply,
						),
					}));
				}
			})
			.catch(() => {
				toast({
					title: t("Error"),
					description: t("You can react to the same comment once a day"),
					variant: "destructive",
				});
			});
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
					ApiService.fetchJson(route("api.entry.comment.list", { slug: entry.slug }))
						.then((response) => {
							setIsCommentsVisible(true);
							setIsCommentsLoading(false);
							setComments(response);
						})
						.finally(() => {
							observer.disconnect();
						});
				}
			},
			{
				rootMargin: "50px",
			},
		);

		if (commentsTriggerRef.current) {
			setIsCommentsLoading(true);
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

				{isCommentsLoading && (
					<div className="flex justify-center py-8">
						<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
					</div>
				)}

				<div ref={commentsTriggerRef} className="h-10"></div>

				{/* Comments Section */}
				<div className="mb-8">
					<h3 className="text-xl font-semibold mb-6 flex items-center">
						<MessageCircle className="h-5 w-5 mr-2" />
						<Trans
							i18nKey="comments"
							values={{ count: commentsCount }}
							components={{ strong: <strong /> }}
						/>
					</h3>

					{authStore.isAuthenticated && (
						<Card className="mb-6">
							<CardContent className="p-4">
								<Textarea
									placeholder={t("Share your thoughts...")}
									value={comment}
									onChange={(e) => setComment(e.target.value)}
									className="mb-4"
									rows={3}
								/>
								{errors.comment && (
									<p className="text-sm text-red-500">{errors.comment}</p>
								)}
								<div className="flex justify-end">
									<Button onClick={() => handleComment()}>Post Comment</Button>
								</div>
							</CardContent>
						</Card>
					)}

					{isCommentsVisible && comments.length > 0 && (
						<div className="space-y-6">
							<Card>
								<CardContent className="p-6">
									{comments.map((comment) => (
										<div
											key={comment.id}
											className="flex items-start space-x-4 mb-3"
										>
											<UserProfileHover username={comment.user.username}>
												<Avatar className="h-10 w-10 cursor-pointer">
													<AvatarImage
														src={
															comment.user.avatar ||
															"/placeholder-avatar.jpg"
														}
														alt={comment.user.name || "User"}
													/>
													<AvatarFallback>
														{comment.user.name
															.split(" ")
															.map((n) => n[0])
															.join("") || "U"}
													</AvatarFallback>
												</Avatar>
											</UserProfileHover>

											<div className="flex-1">
												<div className="flex items-center space-x-2 mb-2">
													<span className="font-medium">
														{comment.user.name}
													</span>
													<span className="text-sm text-muted-foreground">
														{timeAgo(comment.created_at)}
													</span>
												</div>
												<div className="text-muted-foreground">
													<MarkdownRenderer content={comment.content} />
												</div>
												<div className="flex items-center space-x-4">
													<Button
														variant="ghost"
														size="sm"
														disabled={!authStore.isAuthenticated}
														onClick={() => {
															handleCommentLike(comment.id);
														}}
													>
														<Heart
															className={`h-4 w-4 mr-1 ${comment.liked ? "fill-red-900" : ""}`}
														/>{" "}
														{comment.likes}
													</Button>
													{authStore.isAuthenticated && (
														<Button
															onClick={() =>
																toggleReplyTextarea(comment.id)
															}
															variant="ghost"
															size="sm"
														>
															{t("Reply")}
														</Button>
													)}
													{comment.replies > 0 && (
														<Button
															onClick={() =>
																toggleReplies(comment.id)
															}
															variant="ghost"
															size="sm"
														>
															{showReplies[comment.id] ? (
																<ArrowUp className="h-4 w-4 mr-1" />
															) : (
																<ArrowDown className="h-4 w-4 mr-1" />
															)}
															<Trans
																i18nKey="replies"
																values={{ count: comment.replies }}
															/>
														</Button>
													)}
												</div>

												<div className="mt-4 pl-4 border-l-2 border-muted space-y-4">
													{showRepliesTextarea[comment.id] && (
														<Card className="mb-6 animate-fade-in-up">
															<CardContent className="p-4">
																<Textarea
																	placeholder={t(
																		"Share your thoughts...",
																	)}
																	value={
																		repliesComments[comment.id]
																	}
																	onChange={(e) =>
																		setRepliesComments({
																			...repliesComments,
																			[comment.id]:
																				e.target.value,
																		})
																	}
																	className="mb-4"
																	rows={3}
																/>
																{errors[comment.id] && (
																	<p className="text-sm text-red-500">
																		{errors[comment.id]}
																	</p>
																)}
																<div className="flex justify-end">
																	<Button
																		onClick={() =>
																			handleReply(comment.id)
																		}
																	>
																		Post Comment
																	</Button>
																</div>
															</CardContent>
														</Card>
													)}
													{isRepliesLoading[comment.id] && (
														<div className="flex justify-center py-8">
															<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
														</div>
													)}
													{showReplies[comment.id] &&
														replies[comment.id].map((reply) => (
															<div
																key={reply.id}
																className="flex items-start space-x-3"
															>
																<UserProfileHover
																	username={reply.user.username}
																>
																	<Avatar className="h-10 w-10 cursor-pointer">
																		<AvatarImage
																			src={
																				reply.user.avatar ||
																				"/placeholder-avatar.jpg"
																			}
																			alt={
																				reply.user.name ||
																				"User"
																			}
																		/>
																		<AvatarFallback>
																			{reply.user.name
																				.split(" ")
																				.map((n) => n[0])
																				.join("") || "U"}
																		</AvatarFallback>
																	</Avatar>
																</UserProfileHover>
																<div className="flex-1">
																	<div className="flex items-center space-x-2 mb-1">
																		<span className="font-medium">
																			{reply.user.name}
																		</span>
																		<span className="text-sm text-muted-foreground">
																			{timeAgo(
																				reply.created_at,
																			)}
																		</span>
																	</div>
																	<div className="text-sm text-muted-foreground mb-2">
																		<MarkdownRenderer
																			content={reply.content}
																		/>
																	</div>
																	<Button
																		variant="ghost"
																		size="sm"
																		disabled={
																			!authStore.isAuthenticated
																		}
																		onClick={() => {
																			handleCommentLike(
																				reply.id,
																				comment.id,
																			);
																		}}
																	>
																		<Heart
																			className={`h-4 w-4 mr-1 ${reply.liked ? "fill-red-900" : ""}`}
																		/>{" "}
																		{reply.likes}
																	</Button>
																</div>
															</div>
														))}
												</div>
											</div>
										</div>
									))}
								</CardContent>
							</Card>
						</div>
					)}
				</div>

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
