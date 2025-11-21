import { CheckCircle, Circle, Code, FileText, Video } from "lucide-react";
import { ScrollArea } from "../Ui/ScrollArea";
import { Badge } from "../Ui/Badge";
import { Link } from "@inertiajs/react";

export function CourseSidebar({ cirriculum, course, lesson: currentLesson }) {
	const getLessonTypeIcon = (type) => {
		switch (type) {
			case "video":
				return <Video className="h-4 w-4" />;
			case "article":
				return <FileText className="h-4 w-4" />;
			case "exercise":
				return <Code className="h-4 w-4" />;
			default:
				return <Circle className="h-4 w-4" />;
		}
	};

	const getLessonTypeLabel = (type) => {
		switch (type) {
			case "video":
				return "Video";
			case "article":
				return "Article";
			case "exercise":
				return "Exercise";
			default:
				return "Lesson";
		}
	};

	return (
		<ScrollArea className="h-[calc(100vh-4rem)]">
			{cirriculum.map((chapter, chapterIndex) => (
				<div key={chapterIndex} className="border-b last:border-b-0">
					<div className="p-4 bg-muted/30">
						<h4 className="font-medium">{chapter.title}</h4>
						<p className="text-xs text-muted-foreground">
							{chapter.lessons.length} lessons
						</p>
					</div>
					<div className="space-y-1">
						{chapter.lessons.map((lesson, lessonIndex) => {
							const isCompleted = false;
							const isCurrent = currentLesson === lesson.slug;

							return (
								<div
									key={lessonIndex}
									className={`p-3 cursor-pointer hover:bg-muted/50 ${
										isCurrent ? "bg-primary/10 border-r-2 border-r-primary" : ""
									}`}
								>
									<Link
										href={route("course.page", {
											slug: course,
											page: lesson.slug,
										})}
									>
										<div className="flex items-center space-x-3">
											<div className="flex-shrink-0">
												{isCompleted ? (
													<CheckCircle className="h-4 w-4 text-green-500" />
												) : (
													getLessonTypeIcon(lesson.type)
												)}
											</div>
											<div className="flex-1 min-w-0">
												<p
													className={`text-sm font-medium truncate ${
														isCurrent ? "text-primary" : ""
													}`}
												>
													{lesson.title}
												</p>
												<div className="flex items-center space-x-2">
													<Badge
														variant="outline"
														className="text-xs capitalize"
													>
														{getLessonTypeLabel(lesson.type)}
													</Badge>
													<span className="text-xs text-muted-foreground">
														{lesson.duration}
													</span>
												</div>
											</div>
										</div>
									</Link>
								</div>
							);
						})}
					</div>
				</div>
			))}
		</ScrollArea>
	);
}
