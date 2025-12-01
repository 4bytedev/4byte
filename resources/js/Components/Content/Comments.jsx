import { toast } from "@/Hooks/useToast";
import { useTimeAgo } from "@/Lib/TimeAgo";
import { useAuthStore } from "@/Stores/AuthStore";
import { ArrowDown, ArrowUp, Heart, MessageCircle } from "lucide-react";
import { useEffect, useState } from "react";
import { Trans, useTranslation } from "react-i18next";
import { Card, CardContent } from "../Ui/Card";
import { Button } from "../Ui/Form/Button";
import { UserProfileHover } from "../Common/UserProfileHover";
import { Avatar, AvatarFallback, AvatarImage } from "../Ui/Avatar";
import MarkdownRenderer from "../Common/MarkdownRenderer";
import { useMutation } from "@tanstack/react-query";
import ReactApi from "@/Api/ReactApi";
import { CommentSubmitForm } from "./CommentParts/CommentSubmitForm";
import { CommentReplies } from "./CommentParts/CommentReplies";

export function Comments({ commentsCounts: initialCommentsCounts, type, slug }) {
	const [comments, setComments] = useState([]);
	const [showReplies, setShowReplies] = useState({});
	const [isRepliesLoading, setIsRepliesLoading] = useState({});
	const [repliesComments, setRepliesComments] = useState({});
	const [replies, setReplies] = useState({});
	const [commentsCount, setCommentsCount] = useState(Number(initialCommentsCounts));
	const timeAgo = useTimeAgo();
	const { t } = useTranslation();

	const authStore = useAuthStore();

	const fetchComments = useMutation({
		mutationFn: () => ReactApi.comments({ type, slug }),
		onSuccess: (response) => {
			setComments(response);
		},
	});

	useEffect(() => {
		fetchComments.mutate();
	}, []);

	const onCommentSubmit = (data) => {
		setCommentsCount(commentsCount + 1);
		setComments([data, ...comments]);
	};

	const onCommentReplySubmit = (data) => {
		const parentId = data.parent;
		setRepliesComments({ ...repliesComments, [parentId]: "" });
		setCommentsCount(commentsCount + 1);
		setReplies({ ...replies, [parentId]: [data, ...(replies[parentId] ?? [])] });
		setComments((prev) =>
			prev.map((c) => (c.id === parentId ? { ...c, replies: c.replies + 1 } : c)),
		);
	};

	const toggleReplyMutation = useMutation({
		mutationFn: ({ parentId }) => ReactApi.replies({ type, slug, parent: parentId }),
		onMutate: ({ parentId }) => setIsRepliesLoading({ ...isRepliesLoading, [parentId]: true }),
		onSuccess: (response, { parentId }) => {
			console.log(comments);

			setReplies({ ...replies, [parentId]: response });
			setShowReplies({ ...showReplies, [parentId]: true });
			setIsRepliesLoading({ ...isRepliesLoading, [parentId]: false });
		},
	});

	const toggleReplies = (parentId) => {
		if (showReplies[parentId]) {
			setShowReplies({ ...showReplies, [parentId]: false });
		} else {
			if (!replies[parentId]) {
				toggleReplyMutation.mutate({ parentId });
			} else {
				setShowReplies({ ...showReplies, [parentId]: true });
			}
		}
	};

	const likeMutation = useMutation({
		mutationFn: ({ commentId }) => ReactApi.like({ type: "comment", slug: commentId }),
		onSuccess: (_, { commentId, parentId }) => {
			if (!parentId) {
				setComments((prev) =>
					prev.map((c) =>
						c.id === commentId
							? {
									...c,
									likes: c.likes + (c.isLiked ? -1 : 1),
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
		},
		onError: () => {
			toast({
				title: t("Error"),
				description: t("You can react to the same comment once a day"),
				variant: "destructive",
			});
		},
	});

	const handleCommentLike = (commentId, parentId) => {
		if (!authStore.isAuthenticated) return;
		likeMutation.mutate({ commentId, parentId });
	};

	if (fetchComments.isPending) {
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
				<CommentSubmitForm type={type} slug={slug} onSuccess={onCommentSubmit} />
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
												<CommentSubmitForm
													type={type}
													slug={slug}
													parent={comment.id}
													onSuccess={onCommentReplySubmit}
												/>
											)}
											{isRepliesLoading[comment.id] && (
												<div className="flex justify-center py-8">
													<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
												</div>
											)}
											{showReplies[comment.id] && (
												<CommentReplies
													parentId={comment.id}
													replies={replies[comment.id]}
													onLike={handleCommentLike}
												/>
											)}
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
