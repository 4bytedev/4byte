import { Link } from "@inertiajs/react";
import { UserInfo } from "./Shared/UserInfo";
import { Hash, Tag } from "lucide-react";
import { Badge } from "../Ui/Badge";

export function ContentPreviewCard({ item }) {
	const { type } = item;

	switch (type) {
		case "article":
			return (
				<div
					key={item.slug}
					className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50 cursor-pointer transition-colors"
				>
					<div className="flex items-center space-x-2 w-full">
						<img
							src={item.image.image}
							srcSet={item.image.srcset}
							alt={item.title}
							className="h-10 w-10 rounded"
						/>
						<Link href={route("article.view", { slug: item.slug })} className="w-full">
							<span className="text-xs">{item.title}</span>
						</Link>
					</div>
				</div>
			);
		case "user":
			return (
				<Link
					href={route("user.view", {
						username: item.username,
					})}
				>
					<div
						key={item.slug}
						className="rounded-lg hover:bg-accent/50 cursor-pointer transition-colors"
					>
						<UserInfo user={item} />
					</div>
				</Link>
			);
		case "tag":
			return (
				<Link key={item.name} href={route("tag.view", { slug: item.slug })}>
					<div className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50 cursor-pointer transition-colors">
						<div className="flex items-center space-x-2">
							<Hash className="h-3 w-3 text-muted-foreground" />
							<span className="text-sm">{item.name}</span>
						</div>
						<Badge variant="secondary" className="text-xs">
							{item.total}
						</Badge>
					</div>
				</Link>
			);
		case "category":
			return (
				<Link key={item.slug} href={route("category.view", { slug: item.slug })}>
					<div className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50 cursor-pointer transition-colors">
						<div className="flex items-center space-x-2">
							<Tag className="h-3 w-3 text-muted-foreground" />
							<span className="text-sm">{item.name}</span>
						</div>
						<Badge variant="secondary" className="text-xs">
							{item.total}
						</Badge>
					</div>
				</Link>
			);

		default:
			break;
	}
}
