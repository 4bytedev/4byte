import { ArticlePreviewCard } from "./PreviewCard/ArticlePreviewCard";
import { UserPreviewCard } from "./PreviewCard/UserPreviewCard";
import { TagPreviewCard } from "./PreviewCard/TagPreviewCard";
import { CategoryPreviewCard } from "./PreviewCard/CategoryPreviewCard";
import { CoursePreviewCard } from "./PreviewCard/CoursePreviewCard";

export function ContentPreviewCard({ item }) {
	const { type } = item;

	switch (type) {
		case "article":
			return <ArticlePreviewCard {...item} />;
		case "user":
			return <UserPreviewCard {...item} />;
		case "tag":
			return <TagPreviewCard {...item} />;
		case "category":
			return <CategoryPreviewCard {...item} />;
		case "course":
			return <CoursePreviewCard {...item} />;

		default:
			break;
	}
}
