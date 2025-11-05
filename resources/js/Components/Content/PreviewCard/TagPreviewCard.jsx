import { Badge } from "@/Components/Ui/Badge";
import { Link } from "@inertiajs/react";
import { Hash } from "lucide-react";

export function TagPreviewCard({ name, slug, total }) {
	return (
		<Link key={slug} href={route("tag.view", { slug: slug })}>
			<div className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50 cursor-pointer transition-colors">
				<div className="flex items-center space-x-2">
					<Hash className="h-3 w-3 text-muted-foreground" />
					<span className="text-sm">{name}</span>
				</div>
				<Badge variant="secondary" className="text-xs">
					{total}
				</Badge>
			</div>
		</Link>
	);
}
