import { ArticleCard } from "./Card/ArticleCard";
import { CommentCard } from "./Card/CommentCard";
import { DraftCard } from "./Card/DraftCard";
import { EntryCard } from "./Card/EntryCard";
import { UserCard } from "./Card/UserCard";

export function ContentCard({ ...props }) {
	const { type } = props;

	switch (type) {
		case "article":
			return <ArticleCard {...props} />;
		case "draft":
			return <DraftCard {...props} />;
		case "entry":
			return <EntryCard {...props} />;
		case "comment":
			return <CommentCard {...props} />;
		case "user":
			return <UserCard {...props} />;
		default:
			return null;
	}
}
