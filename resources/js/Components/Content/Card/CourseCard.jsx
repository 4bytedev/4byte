import { Tag, Hash } from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Badge } from "@/Components/Ui/Badge";
import { Link } from "@inertiajs/react";
import { UserInfo } from "../Shared/UserInfo";
import { Button } from "@/Components/Ui/Form/Button";

export function CourseCard({ title, slug, excerpt, difficulty, user, tags, categories }) {
	const getDifficultyColor = (difficulty) => {
		switch (difficulty) {
			case "Beginner":
				return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200";
			case "Intermediate":
				return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200";
			case "Advanced":
				return "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200";
			default:
				return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
		}
	};

	const href = route("course.view", { slug });

	return (
		<Card className="hover:shadow-lg transition-shadow">
			<CardHeader>
				<div className="flex items-start justify-between">
					<div className="flex items-center space-x-2">
						<Badge className={getDifficultyColor(difficulty)}>{difficulty}</Badge>
					</div>
				</div>
				<CardTitle className="text-xl">{title}</CardTitle>
				<p className="text-muted-foreground text-sm">{excerpt}</p>
			</CardHeader>

			<CardContent className="space-y-4">
				{/* Instructor */}
				<UserInfo {...user} />

				{/* Tags */}
				<div className="flex flex-wrap gap-1">
					{categories.slice(0, 3).map((category) => (
						<Link
							key={category.slug}
							href={route("category.view", { slug: category.slug })}
						>
							<Badge
								key={category.slug}
								variant="outline"
								className="text-xs p-1 px-2"
							>
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
				</div>

				{/* Action Button */}
				<Button className="w-full" variant="default">
					<Link className="w-full" href={href}>
						{"View Course"}
					</Link>
				</Button>
			</CardContent>
		</Card>
	);
}
