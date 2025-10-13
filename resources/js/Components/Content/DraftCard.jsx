import { Card, CardHeader } from "@/Components/Ui/Card";
import { Link } from "@inertiajs/react";

export function DraftCard({ title, slug }) {
	const href = route("article.crud.edit.view", { slug });

	return (
		<Card className="group hover:shadow-lg transition-all duration-200 overflow-hidden">
			<Link href={href}>
				<CardHeader className="pb-6">
					<h3 className="text-lg font-semibold line-clamp-2 group-hover:text-primary transition-colors">
						{title}
					</h3>
				</CardHeader>
			</Link>
		</Card>
	);
}
