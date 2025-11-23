import { Link } from "@inertiajs/react";

export function CoursePreviewCard({ title, slug, image }) {
	return (
		<div
			key={slug}
			className="flex items-center justify-between p-2 rounded-lg hover:bg-accent/50 cursor-pointer transition-colors"
		>
			<div className="flex items-center space-x-2 w-full">
				<img
					src={image.image}
					srcSet={image.srcset}
					alt={title}
					className="h-10 w-10 rounded"
				/>
				<Link href={route("course.view", { slug: slug })} className="w-full">
					<span className="text-xs">{title}</span>
				</Link>
			</div>
		</div>
	);
}
