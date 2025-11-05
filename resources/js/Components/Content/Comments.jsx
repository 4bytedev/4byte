import { toast } from "@/Hooks/useToast";
import { useTimeAgo } from "@/Lib/TimeAgo";
import ApiService from "@/Services/ApiService";
import { useAuthStore } from "@/Stores/AuthStore";
import { ArrowDown, ArrowUp, Heart, MessageCircle } from "lucide-react";
import { useEffect, useState } from "react";
import { Trans, useTranslation } from "react-i18next";
import { Card, CardContent } from "../Ui/Card";
import { Textarea } from "../Ui/Form/Textarea";
import { Button } from "../Ui/Form/Button";
import { UserProfileHover } from "../Common/UserProfileHover";
import { Avatar, AvatarFallback, AvatarImage } from "../Ui/Avatar";
import MarkdownRenderer from "../Common/MarkdownRenderer";

export function Comments({ commentsCounts: initialCommentsCounts, type, slug }) {
	const [comment, setComment] = useState("");
	const [comments, setComments] = useState([]);
	const [showReplies, setShowReplies] = useState({});
	const [isRepliesLoading, setIsRepliesLoading] = useState({});
	const [repliesComments, setRepliesComments] = useState({});
	const [replies, setReplies] = useState({});
	const [commentsCount, setCommentsCount] = useState(Number(initialCommentsCounts));
	const [errors, setErrors] = useState({});
	const [isCommentsLoading, setIsCommentsLoading] = useState(false);
	const timeAgo = useTimeAgo();
	const { t } = useTranslation();

	const authStore = useAuthStore();

	useEffect(() => {
		ApiService.fetchJson(route("api.react.comments", { type, slug })).then((response) => {
			setIsCommentsLoading(false);
			setComments(response);
		});
	}, []);

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

		ApiService.fetchJson(route("api.react.comment", { type, slug }), data)
			.then((response) => {
				setComment("");
				setCommentsCount(commentsCount + 1);
				setComments([response, ...comments]);
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
			});
	};

	const handleReply = (parentId) => {
		setErrors({});

		if (!parentId) return;
		if (!validateReply(parentId)) return;

		const data = {
			content: repliesComments[parentId],
			parent: parentId,
		};

		ApiService.fetchJson(route("api.react.comment", { type, slug }), data).then((response) => {
			setRepliesComments({ ...repliesComments, [parentId]: "" });
			setCommentsCount(commentsCount + 1);
			if (!parentId) {
				setComments([response, ...comments]);
			} else {
				setReplies({ ...replies, [parentId]: [response, ...(replies[parentId] ?? [])] });
				setComments((prev) =>
					prev.map((c) => (c.id === parentId ? { ...c, replies: c.replies + 1 } : c)),
				);
			}
		});
	};

	const toggleReplies = (parentId) => {
		if (showReplies[parentId]) {
			setShowReplies({ ...showReplies, [parentId]: false });
		} else {
			if (!replies[parentId]) {
				setIsRepliesLoading({ ...isRepliesLoading, [parentId]: true });
				ApiService.fetchJson(
					route("api.react.comment.replies", {
						type,
						slug,
						parent: parentId,
					}),
				).then((response) => {
					setReplies({ ...replies, [parentId]: response });
					setShowReplies({ ...showReplies, [parentId]: true });
					setIsRepliesLoading({ ...isRepliesLoading, [parentId]: false });
				});
			} else {
				setShowReplies({ ...showReplies, [parentId]: true });
			}
		}
	};

	const handleCommentLike = (commentId, parentId) => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.like", { type: "comment", slug: commentId }))
			.then(() => {
				if (!parentId) {
					setComments((prev) =>
						prev.map((c) =>
							c.id === commentId
								? {
										...c,
										likes: c.likes + (c.isLiked ? -1 : +1),
										isLiked: !c.isLiked,
									}
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
										likes: reply.likes + (reply.isLiked ? -1 : 1),
										isLiked: !reply.isLiked,
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

	if (isCommentsLoading) {
		return (
			<div className="flex justify-center py-8">
				<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
			</div>
		);
	}

	return (
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
						{errors.comment && <p className="text-sm text-red-500">{errors.comment}</p>}
						<div className="flex justify-end">
							<Button onClick={() => handleComment()}>Post Comment</Button>
						</div>
					</CardContent>
				</Card>
			)}

			{comments.length > 0 && (
				<div className="space-y-6">
					<Card>
						<CardContent className="p-6">
							{comments.map((comment) => (
								<div key={comment.id} className="flex items-start space-x-4 mb-3">
									<UserProfileHover username={comment.user.username}>
										<Avatar className="h-10 w-10 cursor-pointer">
											<AvatarImage
												src={
													comment.user.avatar || "/placeholder-avatar.jpg"
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
											<span className="font-medium">{comment.user.name}</span>
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
													className={`h-4 w-4 mr-1 ${comment.isLiked ? "fill-red-900" : ""}`}
												/>{" "}
												{comment.likes}
											</Button>
											{authStore.isAuthenticated && (
												<Button
													onClick={() => toggleReplies(comment.id)}
													variant="ghost"
													size="sm"
												>
													{comment.replies > 0 &&
														(showReplies[comment.id] ? (
															<ArrowUp className="h-4 w-4 mr-1" />
														) : (
															<ArrowDown className="h-4 w-4 mr-1" />
														))}
													{comment.replies > 0 ? (
														<Trans
															i18nKey="replies"
															values={{
																count: comment.replies,
															}}
														/>
													) : (
														t("Reply")
													)}
												</Button>
											)}
										</div>

										<div className="mt-4 pl-4 border-l-2 border-muted space-y-4">
											{showReplies[comment.id] && (
												<Card className="mb-6 animate-fade-in-up">
													<CardContent className="p-4">
														<Textarea
															placeholder={t(
																"Share your thoughts...",
															)}
															value={repliesComments[comment.id]}
															onChange={(e) =>
																setRepliesComments({
																	...repliesComments,
																	[comment.id]: e.target.value,
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
																	alt={reply.user.name || "User"}
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
																	{timeAgo(reply.created_at)}
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
																	className={`h-4 w-4 mr-1 ${reply.isLiked ? "fill-red-900" : ""}`}
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
	);
}
