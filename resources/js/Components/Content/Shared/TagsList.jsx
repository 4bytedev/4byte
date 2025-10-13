import { Badge } from "@/Components/Ui/Badge";
import { Tag, Hash } from "lucide-react";

export function TagsList({ tags = [], categories = [] }) {
	const total = tags.length + categories.length;
	return (
		<div className="flex flex-wrap gap-1">
			{categories.slice(0, 3).map((category) => (
				<Badge key={category.slug} variant="outline" className="text-xs p-1 px-2">
					<Tag className="h-4 w-4 mr-1" />
					{category.name}
				</Badge>
			))}
			{tags.slice(0, 3).map((tag) => (
				<Badge key={tag.slug} variant="outline" className="text-xs p-1 px-2">
					<Hash className="h-4 w-4 mr-1" />
					{tag.name}
				</Badge>
			))}
			{total > 6 && (
				<Badge variant="outline" className="text-xs">
					+{total - 6}
				</Badge>
			)}
		</div>
	);
}
