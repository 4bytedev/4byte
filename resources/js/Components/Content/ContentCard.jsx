import { ArticleCard } from "./ArticleCard";
import { CommentCard } from "./CommentCard";
import { DraftCard } from "./DraftCard";
import { EntryCard } from "./EntryCard";

export function ContentCard(props) {
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
		default:
			return <ArticleCard {...props} />;
	}
}
