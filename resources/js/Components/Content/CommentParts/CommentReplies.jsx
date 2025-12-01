import MarkdownRenderer from "@/Components/Common/MarkdownRenderer";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Form/Button";
import { useTimeAgo } from "@/Lib/TimeAgo";
import { useAuthStore } from "@/Stores/AuthStore";
import { Heart } from "lucide-react";

export function CommentReplies({ replies, parentId, onLike }) {
	const timeAgo = useTimeAgo();
	const authStore = useAuthStore();

	return (
		<>
			{replies.map((reply) => (
				<div key={reply.id} className="flex items-start space-x-3">
					<UserProfileHover username={reply.user.username}>
						<Avatar className="h-10 w-10 cursor-pointer">
							<AvatarImage
								src={reply.user.avatar || "/placeholder-avatar.jpg"}
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
							<span className="font-medium">{reply.user.name}</span>
							<span className="text-sm text-muted-foreground">
								{timeAgo(reply.created_at)}
							</span>
						</div>
						<div className="text-sm text-muted-foreground mb-2">
							<MarkdownRenderer content={reply.content} />
						</div>
						<Button
							variant="ghost"
							size="sm"
							disabled={!authStore.isAuthenticated}
							onClick={() => {
								onLike(reply.id, parentId);
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
		</>
	);
}
