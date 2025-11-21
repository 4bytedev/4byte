import { useEffect, useState } from "react";
import { CheckCircle, Circle, ChevronLeft, ChevronRight } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { SidebarOverlay, SidebarRoot } from "@/Components/Ui/Sidebar";
import { useSidebar } from "@/Contexts/SidebarContext";
import { useDevice } from "@/Hooks/useDevice";
import { CourseSidebar } from "@/Components/Sidebar/CourseSidebar";
import MarkdownRenderer from "@/Components/Common/MarkdownRenderer";
import { Link } from "@inertiajs/react";
import ReactPlayer from "react-player";

export default function TutorialPage({ course, lesson, cirriculum }) {
	const [completedLessons, setCompletedLessons] = useState([0, 1]);
	const { setIsVisible, setIsOpen, isOpen } = useSidebar();
	const { isMobile } = useDevice();

	useEffect(() => {
		setIsVisible(true);
		setIsOpen(!isMobile);
		return () => setIsVisible(false);
	}, []);

	const toggleLessonComplete = (lessonIndex) => {
		if (completedLessons.includes(lessonIndex)) {
			setCompletedLessons((prev) => prev.filter((i) => i !== lessonIndex));
		} else {
			setCompletedLessons((prev) => [...prev, lessonIndex]);
		}
	};

	const currentSlug = lesson.slug;

	const allLessons = cirriculum
		.flatMap((ch) => ch.lessons || [])
		.filter((lesson) => lesson && lesson.slug);

	const currentIndex = allLessons.findIndex((lesson) => lesson.slug === currentSlug);

	const prevLesson = currentIndex > 0 ? allLessons[currentIndex - 1] : null;

	const nextLesson =
		currentIndex >= 0 && currentIndex < allLessons.length - 1
			? allLessons[currentIndex + 1]
			: null;

	return (
		<div className="flex">
			{isOpen && (
				<div className="lg:w-72 md:w-48 sm:w-32 w-64 w-full sticky sm:top-16 h-[calc(100vh-4rem)]">
					<SidebarOverlay />
					<SidebarRoot>
						<CourseSidebar
							cirriculum={cirriculum}
							course={course}
							lesson={currentSlug}
						/>
					</SidebarRoot>
				</div>
			)}
			<div className="container mx-auto px-4 py-8">
				<div className="max-w-4xl mx-auto">
					<div className="flex items-center justify-between mb-4 pb-4 border-b">
						<h2 className="text-xl font-semibold">{lesson.title}</h2>
						<Button variant="outline" onClick={() => toggleLessonComplete(currentSlug)}>
							{completedLessons.includes(currentSlug) ? (
								<CheckCircle className="h-4 w-4 mr-2" />
							) : (
								<Circle className="h-4 w-4 mr-2" />
							)}
							{completedLessons.includes(currentSlug) ? "Completed" : "Mark Complete"}
						</Button>
					</div>

					{lesson.video_url ? (
						<div className="space-y-4">
							<div className="aspect-video bg-muted rounded-lg flex items-center justify-center">
								<ReactPlayer
									src={lesson.video_url}
									pip={false}
									width="100%"
									height="100%"
									className="rounded-lg"
								/>
							</div>
							{lesson.content && <MarkdownRenderer content={lesson.content} />}
						</div>
					) : (
						<MarkdownRenderer content={lesson.content} />
					)}

					<div className="flex items-center justify-between mt-6 pt-6 border-t">
						<Button variant="outline" type="button" disabled={prevLesson == null}>
							<Link
								className="flex"
								href={route("course.page", {
									slug: course,
									page: prevLesson ? prevLesson.slug : "",
								})}
							>
								<ChevronLeft className="h-4 w-4 mr-2" />
								Previous
							</Link>
						</Button>
						<span className="text-sm text-muted-foreground">
							Lesson {currentIndex + 1} of {allLessons.length}
						</span>
						<Button variant="outline" type="button" disabled={nextLesson == null}>
							<Link
								className="flex"
								href={route("course.page", {
									slug: course,
									page: nextLesson ? nextLesson.slug : "",
								})}
							>
								Next
								<ChevronRight className="h-4 w-4 ml-2" />
							</Link>
						</Button>
					</div>
				</div>
			</div>
		</div>
	);
}
