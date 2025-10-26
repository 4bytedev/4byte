import { Calendar, Clock, Tag, Hash } from "lucide-react";
import { Card, CardContent, CardHeader } from "@/Components/Ui/Card";
import { Badge } from "@/Components/Ui/Badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { UserProfileHover } from "@/Components/Ui/UserProfileHover";
import { Link } from "@inertiajs/react";

export function ArticleCard({
	title,
	slug,
	excerpt,
	user,
	published_at,
	readTime,
	tags,
	categories,
	image,
}) {
	const href = route("article.view", { slug });

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
									<div className="font-medium">{user.name}</div>
									<div className="text-muted-foreground">@{user.username}</div>
								</div>
							</div>
						</div>
					</UserProfileHover>
					<div className="flex items-center space-x-1">
						{categories.slice(0, 3).map((category) => (
							<Link
								key={category.slug}
								href={route("category.view", { slug: category.slug })}
							>
								<Badge variant="outline" className="text-xs p-1 px-2">
									<Tag className="h-4 w-4 mr-1" />
									{category.name}
								</Badge>
							</Link>
						))}
						{tags.slice(0, 3).map((tag) => (
							<Link key={tag.slug} href={route("tag.view", { slug: tag.slug })}>
								<Badge variant="outline" className="text-xs p-1 px-2">
									<Hash className="h-4 w-4 mr-1" />
									{tag.name}
								</Badge>
							</Link>
						))}
						{tags.length + categories.length > 3 && (
							<Badge variant="outline" className="text-xs">
								+{tags.length + categories.length - 6}
							</Badge>
						)}
					</div>
				</div>
			</CardHeader>

			<div className="relative h-64 w-full overflow-hidden">
				<Link href={href}>
					<img
						src={image.image}
						srcSet={image.srcset}
						alt={title}
						className="h-full w-full object-cover object-center transition-transform duration-1000 group-hover:scale-105"
					/>
					<div className="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent" />
				</Link>
			</div>
			<CardHeader className="pb-3">
				<div className="flex items-start justify-between">
					<div className="flex items-center space-x-2 text-sm text-muted-foreground">
						{published_at && (
							<>
								<Calendar className="h-3 w-3" />
								<span>{new Date(published_at).toLocaleDateString()}</span>
							</>
						)}
						{readTime && (
							<>
								<Clock className="h-3 w-3 ml-2" />
								<span>{readTime}</span>
							</>
						)}
					</div>
				</div>
				<Link href={href}>
					<h3 className="text-lg font-semibold line-clamp-2 group-hover:text-primary transition-colors">
						{title}
					</h3>
				</Link>
			</CardHeader>
			<CardContent>
				<p className="text-muted-foreground line-clamp-3 mb-4">{excerpt}</p>
			</CardContent>
		</Card>
	);
}
