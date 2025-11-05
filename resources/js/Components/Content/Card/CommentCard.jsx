import { Calendar, Heart, MessageSquare } from "lucide-react";
import { Card, CardContent, CardHeader } from "@/Components/Ui/Card";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";
import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";

export function CommentCard({
	content,
	parent,
	published_at,
	user,
	replies,
	likes,
	isLiked,
	content_type,
	content_title,
	content_slug,
}) {
	const contentHref =
		content_slug && route().has(`${content_type}.view`)
			? route(`${content_type}.view`, { slug: content_slug })
			: "#";

	const { t } = useTranslation();

	return (
		<Card className="group hover:shadow-lg transition-all duration-200 overflow-hidden">
			<CardHeader className="p-2">
				<div className="flex items-center space-x-2">
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
									<div className="font-medium">{user.name}</div>
									<div className="text-muted-foreground">@{user.username}</div>
								</div>
							</div>
						</div>
					</UserProfileHover>
				</div>
			</CardHeader>

			<CardContent>
				{parent && (
					<div className="text-xs text-muted-foreground mb-1">
						{t("Replying to comment")}
					</div>
				)}
				<p className="text-muted-foreground">{content}</p>
			</CardContent>

			<CardHeader className="pt-0 pb-2">
				<div className="flex items-center justify-between text-sm text-muted-foreground">
					<div className="flex items-center space-x-2">
						{published_at && (
							<>
								<Calendar className="h-3 w-3" />
								<span>{new Date(published_at).toLocaleDateString()}</span>
							</>
						)}
					</div>
					<div className="flex items-center space-x-4">
						<div className="flex items-center space-x-1">
							<Heart className={`h-3 w-3 ${isLiked ? "text-red-500" : ""}`} />
							<span>{likes}</span>
						</div>
						<div className="flex items-center space-x-1">
							<MessageSquare className="h-3 w-3" />
							<span>{replies}</span>
						</div>
					</div>
				</div>
				{content_type && content_title && (
					<div className="mt-1 text-xs text-primary">
						{t("In response to:")}{" "}
						<Link href={contentHref} className="underline">
							{content_title}
						</Link>
					</div>
				)}
			</CardHeader>
		</Card>
	);
}
