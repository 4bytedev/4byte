import {
	Circle,
	FileText,
	Code,
	Video,
	Hash,
	Tag,
	ThumbsUp,
	ThumbsDown,
	Bookmark,
	Check,
	Share2,
} from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Form/Button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Badge } from "@/Components/Ui/Badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Components/Ui/Tabs";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";
import MarkdownRenderer from "@/Components/Common/MarkdownRenderer";
import { Link } from "@inertiajs/react";
import { useState } from "react";
import { useAuthStore } from "@/Stores/AuthStore";
import ApiService from "@/Services/ApiService";
import { toast, useToast } from "@/Hooks/useToast";

export default function TutorialPage({ course, cirriculum }) {
	const [isLiked, setIsLiked] = useState(course.isLiked);
	const [isDisliked, setIsDisliked] = useState(course.isDisliked);
	const [likes, setLikes] = useState(Number(course.likes));
	const [dislikes, setDislikes] = useState(Number(course.dislikes));
	const [isSaved, setIsSaved] = useState(course.isSaved);
	const [isCopied, setIsCopied] = useState(false);

	const authStore = useAuthStore();
	const { t } = useToast();

	const handleLike = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.like", { type: "course", slug: course.slug }))
			.then(() => {
				if (isLiked) {
					setIsLiked(false);
					setLikes(likes - 1);
				} else {
					if (isDisliked) {
						setIsDisliked(false);
						setDislikes(dislikes - 1);
					}
					setIsLiked(true);
					setLikes(likes + 1);
				}
			})
			.catch(() => {
				toast({
					title: t("Error"),
					description: t("You can react to the same article once a day"),
					variant: "destructive",
				});
			});
	};

	const handleDislike = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.dislike", { type: "course", slug: course.slug }))
			.then(() => {
				if (isDisliked) {
					setIsDisliked(false);
					setDislikes(dislikes - 1);
				} else {
					if (isLiked) {
						setIsLiked(false);
						setLikes(likes - 1);
					}
					setIsDisliked(true);
					setDislikes(dislikes + 1);
				}
			})
			.catch(() => {
				toast({
					title: t("Error"),
					description: t("You can react to the same article once a day"),
					variant: "destructive",
				});
			});
	};

	const handleSave = () => {
		if (!authStore.isAuthenticated) return;
		ApiService.fetchJson(route("api.react.save", { type: "course", slug: course.slug })).then(
			() => {
				setIsSaved(!isSaved);
			},
		);
	};

	const handleShare = () => {
		if (navigator.share) {
			navigator.share({
				url: route("course.view", { slug: course.slug }),
			});
		} else {
			navigator.clipboard.writeText(route("course.view", { slug: course.slug }));
			setIsCopied(true);
			setTimeout(() => {
				setIsCopied(false);
			}, 1500);
		}
	};

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

	const firstLesson = cirriculum
		.map((ch) => ch.lessons)
		.find((lessons) => lessons.length > 0)?.[0];

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-6xl mx-auto">
				<div className="grid lg:grid-cols-3 gap-8 mb-8">
					<div className="lg:col-span-2">
						<div className="flex items-center space-x-2 text-sm text-muted-foreground mb-4">
							<Badge className={getDifficultyColor(course.difficulty)}>
								{course.difficulty}
							</Badge>
							{course.categories.slice(0, 3).map((category) => (
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
							{course.tags.slice(0, 3).map((tag) => (
								<Link key={tag.slug} href={route("tag.view", { slug: tag.slug })}>
									<Badge variant="outline" className="text-xs p-1 px-2">
										<Hash className="h-4 w-4 mr-1" />
										{tag.name}
									</Badge>
								</Link>
							))}
						</div>

						<h1 className="text-3xl font-bold mb-4">{course.title}</h1>
						<p className="text-muted-foreground text-lg mb-6">{course.excerpt}</p>

						<div className="flex items-center space-x-2 text-sm">
							<Button
								variant={isLiked ? "default" : "outline"}
								size="sm"
								disabled={!authStore.isAuthenticated}
								onClick={handleLike}
							>
								<ThumbsUp
									className={`h-4 w-4 mr-1 ${isLiked ? "fill-current" : ""}`}
								/>
								{likes}
							</Button>
							<Button
								variant={isDisliked ? "default" : "outline"}
								size="sm"
								disabled={!authStore.isAuthenticated}
								onClick={handleDislike}
							>
								<ThumbsDown
									className={`h-4 w-4 mr-1 ${isDisliked ? "fill-current" : ""}`}
								/>
								{dislikes}
							</Button>
							<Button
								variant={isSaved ? "default" : "outline"}
								size="sm"
								disabled={!authStore.isAuthenticated}
								onClick={handleSave}
							>
								<Bookmark className={`h-4 w-4 ${isSaved ? "fill-current" : ""}`} />
							</Button>
							<Button variant="outline" size="sm" onClick={handleShare}>
								{isCopied ? (
									<Check className="h-4 w-4" />
								) : (
									<Share2 className="h-4 w-4" />
								)}
							</Button>
						</div>
					</div>

					<div className="space-y-4">
						<Card>
							<CardContent className="p-6">
								<UserProfileHover username={course.user.username}>
									<div className="flex items-center space-x-3 cursor-pointer">
										<Avatar className="h-12 w-12">
											<AvatarImage
												src={course.user.avatar}
												alt={course.user.name}
											/>
											<AvatarFallback>
												{course.user.name
													.split(" ")
													.map((n) => n[0])
													.join("")}
											</AvatarFallback>
										</Avatar>
										<div>
											<p className="font-medium">{course.user.name}</p>
											<p className="text-sm text-muted-foreground">
												@{course.user.username}
											</p>
										</div>
									</div>
								</UserProfileHover>

								<div className="text-center">
									<div className="text-2xl font-bold mb-2">Free</div>
									<Button className="w-full" size="lg" type="button">
										<Link
											className="w-full"
											href={route("course.page", {
												slug: course.slug,
												page: firstLesson.slug,
											})}
										>
											{"Start Course"}
										</Link>
									</Button>
								</div>
							</CardContent>
						</Card>
					</div>
				</div>
				<Tabs defaultValue="overview" className="w-full">
					<TabsList className="grid w-full grid-cols-2">
						<TabsTrigger value="overview">Course Overview</TabsTrigger>
						<TabsTrigger value="curriculum">Curriculum</TabsTrigger>
					</TabsList>

					<TabsContent value="overview" className="mt-6">
						<MarkdownRenderer content={course.content} />
					</TabsContent>

					<TabsContent value="curriculum" className="mt-6">
						<div className="space-y-4">
							{cirriculum.map((chapter, chapterIndex) => (
								<Card key={chapterIndex}>
									<CardHeader>
										<CardTitle className="text-lg">{chapter.title}</CardTitle>
									</CardHeader>
									<CardContent>
										<div className="space-y-2">
											{chapter.lessons.map((lesson, lessonIndex) => (
												<div
													key={lessonIndex}
													className="flex items-center justify-between p-2 rounded border"
												>
													<div className="flex items-center space-x-3">
														{getLessonTypeIcon(lesson.type)}
														<Link
															href={route("course.page", {
																slug: course.slug,
																page: lesson.slug,
															})}
														>
															<span className="font-medium">
																{lesson.title}
															</span>
														</Link>
														<Badge
															variant="outline"
															className="text-xs capitalize"
														>
															{getLessonTypeLabel(lesson.type)}
														</Badge>
													</div>
												</div>
											))}
										</div>
									</CardContent>
								</Card>
							))}
						</div>
					</TabsContent>
				</Tabs>
			</div>
		</div>
	);
}
