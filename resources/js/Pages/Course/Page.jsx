import { useEffect, useState } from "react";
import {
	Play,
	BookOpen,
	CheckCircle,
	Circle,
	ChevronLeft,
	ChevronRight,
	FileText,
	Code,
	Video,
} from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Badge } from "@/Components/Ui/Badge";
import { ScrollArea } from "@/Components/Ui/ScrollArea";
import { SidebarOverlay, SidebarRoot } from "@/Components/Ui/Sidebar";
import { useSidebar } from "@/Contexts/SidebarContext";
import { useDevice } from "@/Hooks/useDevice";

export default function TutorialPage() {
	const [currentLesson, setCurrentLesson] = useState(0);
	const [completedLessons, setCompletedLessons] = useState([0, 1]);
	const { setIsVisible, setIsOpen, isOpen } = useSidebar();
	const { isMobile } = useDevice();

	useEffect(() => {
		setIsVisible(true);
		setIsOpen(!isMobile);
		return () => setIsVisible(false);
	}, []);

	// Mock tutorial data - in real app, fetch based on params.slug
	const tutorial = {
		title: "Complete React Developer Course",
		description:
			"Master React from basics to advanced concepts including hooks, context, performance optimization, and modern development patterns.",
		instructor: {
			name: "Sarah Johnson",
			username: "sarahjohnson",
			avatar: "/placeholder-avatar.jpg",
			role: "Senior React Developer",
			bio: "Passionate about React, TypeScript, and building scalable web applications.",
			location: "San Francisco, CA",
			joinedDate: "2022-01-15",
			followers: 1234,
			following: 567,
			articles: 42,
			tags: ["React", "TypeScript", "JavaScript", "Frontend"],
		},
		difficulty: "Beginner",
		duration: "24 hours",
		totalLessons: 48,
		enrolled: 1234,
		rating: 4.8,
		reviews: 156,
		price: "Free",
		tags: ["React", "JavaScript", "Frontend", "Hooks"],
		chapters: [
			{
				title: "Introduction to React",
				lessons: [
					{
						title: "What is React?",
						duration: "12:34",
						type: "video",
						description: "Introduction to React and its core concepts",
						content: {
							videoUrl: "https://example.com/video1.mp4",
							transcript: "Welcome to React fundamentals...",
						},
					},
					{
						title: "React Fundamentals Guide",
						duration: "15:00",
						type: "article",
						description: "Comprehensive written guide to React basics",
						content: {
							markdown: `# React Fundamentals

React is a JavaScript library for building user interfaces. It was created by Facebook and is now maintained by Facebook and the community.

## Key Concepts

### Components
Components are the building blocks of React applications. They let you split the UI into independent, reusable pieces.

### JSX
JSX is a syntax extension for JavaScript that looks similar to XML or HTML.

### Props
Props are how you pass data from parent components to child components.

### State
State is how you store and manage data that can change over time in your component.`,
						},
					},
					{
						title: "Build Your First Component",
						duration: "30:00",
						type: "exercise",
						description:
							"Hands-on coding exercise to create your first React component",
						content: {
							instructions:
								"Create a simple React component that displays a greeting message",
							starterCode: `import React from 'react';

function Greeting() {
  // Your code here
  return (
    <div>
      {/* Add your greeting message */}
    </div>
  );
}

export default Greeting;`,
							solution: `import React from 'react';

function Greeting({ name = "World" }) {
  return (
    <div>
      <h1>Hello, {name}!</h1>
      <p>Welcome to React!</p>
    </div>
  );
}

export default Greeting;`,
							tests: [
								"Component renders without crashing",
								"Displays greeting message",
								"Accepts name prop",
							],
						},
					},
					{
						title: "React Setup Video Tutorial",
						duration: "18:45",
						type: "video",
						description:
							"Step-by-step video guide to setting up React development environment",
						content: {
							videoUrl: "https://example.com/video2.mp4",
							transcript:
								"In this video, we'll set up our React development environment...",
						},
					},
				],
			},
			{
				title: "Components and Props",
				lessons: [
					{
						title: "Understanding Props",
						duration: "16:30",
						type: "video",
						description: "How to pass data between components using props",
						content: {
							videoUrl: "https://example.com/video3.mp4",
							transcript:
								"Props are the way we pass data from parent to child components...",
						},
					},
					{
						title: "Component Composition Guide",
						duration: "20:00",
						type: "article",
						description:
							"Written guide on building complex UIs with component composition",
						content: {
							markdown: `# Component Composition

Component composition is a powerful pattern in React that allows you to build complex UIs by combining simpler components.

## Benefits of Composition

- **Reusability**: Components can be reused across different parts of your application
- **Maintainability**: Smaller components are easier to understand and maintain
- **Testability**: Individual components can be tested in isolation

## Composition Patterns

### Children Props
The most basic form of composition uses the \`children\` prop.

### Render Props
A technique for sharing code between React components using a prop whose value is a function.`,
						},
					},
					{
						title: "Props Exercise",
						duration: "25:00",
						type: "exercise",
						description: "Practice passing and using props in React components",
						content: {
							instructions:
								"Create a UserCard component that accepts user data as props",
							starterCode: `import React from 'react';

function UserCard(props) {
  // Implement the UserCard component
  return (
    <div>
      {/* Your implementation here */}
    </div>
  );
}

export default UserCard;`,
							solution: `import React from 'react';

function UserCard({ name, email, avatar, role }) {
  return (
    <div className="user-card">
      <img src={avatar} alt={name} />
      <h3>{name}</h3>
      <p>{email}</p>
      <span className="role">{role}</span>
    </div>
  );
}

export default UserCard;`,
							tests: [
								"Component accepts props correctly",
								"Displays user information",
								"Handles missing props gracefully",
							],
						},
					},
				],
			},
		],
	};

	const allLessons = tutorial.chapters.flatMap((chapter, chapterIndex) =>
		chapter.lessons.map((lesson, lessonIndex) => ({
			...lesson,
			chapterIndex,
			lessonIndex,
			globalIndex:
				tutorial.chapters
					.slice(0, chapterIndex)
					.reduce((acc, ch) => acc + ch.lessons.length, 0) + lessonIndex,
		})),
	);

	const currentLessonData = allLessons[currentLesson];

	const toggleLessonComplete = (lessonIndex) => {
		if (completedLessons.includes(lessonIndex)) {
			setCompletedLessons((prev) => prev.filter((i) => i !== lessonIndex));
		} else {
			setCompletedLessons((prev) => [...prev, lessonIndex]);
		}
	};

	const nextLesson = () => {
		if (currentLesson < allLessons.length - 1) {
			setCurrentLesson(currentLesson + 1);
		}
	};

	const prevLesson = () => {
		if (currentLesson > 0) {
			setCurrentLesson(currentLesson - 1);
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

	const renderLessonContent = () => {
		const lesson = currentLessonData;

		switch (lesson.type) {
			case "video":
				return (
					<div className="space-y-4">
						<div className="aspect-video bg-muted rounded-lg flex items-center justify-center">
							<div className="text-center">
								<Play className="h-16 w-16 mx-auto mb-4 text-muted-foreground" />
								<p className="text-muted-foreground">Video Player</p>
								<p className="text-sm text-muted-foreground">{lesson.title}</p>
							</div>
						</div>
						{lesson.content.transcript && (
							<Card>
								<CardHeader>
									<CardTitle className="text-lg">Transcript</CardTitle>
								</CardHeader>
								<CardContent>
									<p className="text-muted-foreground">
										{lesson.content.transcript}
									</p>
								</CardContent>
							</Card>
						)}
					</div>
				);

			case "article":
				return (
					<Card>
						<CardContent className="p-6">
							<div className="prose prose-lg dark:prose-invert max-w-none">
								<div
									dangerouslySetInnerHTML={{
										__html:
											lesson.content.markdown?.replace(/\n/g, "<br />") || "",
									}}
								/>
							</div>
						</CardContent>
					</Card>
				);

			case "exercise":
				return (
					<div className="space-y-6">
						<Card>
							<CardHeader>
								<CardTitle className="text-lg">Instructions</CardTitle>
							</CardHeader>
							<CardContent>
								<p className="text-muted-foreground mb-4">
									{lesson.content.instructions}
								</p>

								<div className="space-y-4">
									<div>
										<h4 className="font-semibold mb-2">Starter Code:</h4>
										<pre className="bg-muted p-4 rounded-lg overflow-x-auto">
											<code>{lesson.content.starterCode}</code>
										</pre>
									</div>

									<div>
										<h4 className="font-semibold mb-2">Tests to Pass:</h4>
										<ul className="list-disc list-inside space-y-1">
											{lesson.content.tests?.map((test, index) => (
												<li key={index} className="text-muted-foreground">
													{test}
												</li>
											))}
										</ul>
									</div>
								</div>
							</CardContent>
						</Card>

						<Card>
							<CardHeader>
								<CardTitle className="text-lg">Code Editor</CardTitle>
							</CardHeader>
							<CardContent>
								<div className="bg-muted p-4 rounded-lg min-h-[200px] font-mono text-sm">
									<p className="text-muted-foreground">
										Interactive code editor would go here
									</p>
								</div>
								<div className="flex justify-between mt-4">
									<Button variant="outline">Reset Code</Button>
									<div className="space-x-2">
										<Button variant="outline">Run Tests</Button>
										<Button>Submit Solution</Button>
									</div>
								</div>
							</CardContent>
						</Card>
					</div>
				);

			default:
				return (
					<div className="aspect-video bg-muted rounded-lg flex items-center justify-center">
						<div className="text-center">
							<BookOpen className="h-16 w-16 mx-auto mb-4 text-muted-foreground" />
							<p className="text-muted-foreground">Content not available</p>
						</div>
					</div>
				);
		}
	};

	return (
		<div className="flex">
			{isOpen && (
				<div className="lg:w-72 md:w-48 sm:w-32 w-64 w-full sticky sm:top-16 h-[calc(100vh-4rem)]">
					<SidebarOverlay />
					<SidebarRoot>
						<ScrollArea className="h-[calc(100vh-4rem)]">
							{tutorial.chapters.map((chapter, chapterIndex) => (
								<div key={chapterIndex} className="border-b last:border-b-0">
									<div className="p-4 bg-muted/30">
										<h4 className="font-medium">{chapter.title}</h4>
										<p className="text-xs text-muted-foreground">
											{chapter.lessons.length} lessons
										</p>
									</div>
									<div className="space-y-1">
										{chapter.lessons.map((lesson, lessonIndex) => {
											const globalIndex =
												tutorial.chapters
													.slice(0, chapterIndex)
													.reduce(
														(acc, ch) => acc + ch.lessons.length,
														0,
													) + lessonIndex;
											const isCompleted =
												completedLessons.includes(globalIndex);
											const isCurrent = currentLesson === globalIndex;

											return (
												<div
													key={lessonIndex}
													className={`p-3 cursor-pointer hover:bg-muted/50 ${
														isCurrent
															? "bg-primary/10 border-r-2 border-r-primary"
															: ""
													}`}
													onClick={() => setCurrentLesson(globalIndex)}
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
																	{getLessonTypeLabel(
																		lesson.type,
																	)}
																</Badge>
																<span className="text-xs text-muted-foreground">
																	{lesson.duration}
																</span>
															</div>
														</div>
													</div>
												</div>
											);
										})}
									</div>
								</div>
							))}
						</ScrollArea>
					</SidebarRoot>
				</div>
			)}
			<div className="container mx-auto px-4 py-8">
				<div className="max-w-4xl mx-auto">
					<Card className="mb-6">
						<CardContent className="p-6">
							<div className="flex items-center justify-between mb-4">
								<div className="flex items-center space-x-3">
									<div className="flex items-center space-x-2">
										{getLessonTypeIcon(currentLessonData?.type)}
										<Badge variant="outline" className="capitalize">
											{getLessonTypeLabel(currentLessonData?.type)}
										</Badge>
									</div>
									<div>
										<h2 className="text-xl font-semibold">
											{currentLessonData?.title}
										</h2>
										<p className="text-muted-foreground">
											{currentLessonData?.description}
										</p>
									</div>
								</div>
								<Button
									variant="outline"
									onClick={() => toggleLessonComplete(currentLesson)}
								>
									{completedLessons.includes(currentLesson) ? (
										<CheckCircle className="h-4 w-4 mr-2" />
									) : (
										<Circle className="h-4 w-4 mr-2" />
									)}
									{completedLessons.includes(currentLesson)
										? "Completed"
										: "Mark Complete"}
								</Button>
							</div>

							{renderLessonContent()}

							<div className="flex items-center justify-between mt-6 pt-6 border-t">
								<Button
									variant="outline"
									onClick={prevLesson}
									disabled={currentLesson === 0}
								>
									<ChevronLeft className="h-4 w-4 mr-2" />
									Previous
								</Button>
								<span className="text-sm text-muted-foreground">
									Lesson {currentLesson + 1} of {allLessons.length}
								</span>
								<Button
									onClick={nextLesson}
									disabled={currentLesson === allLessons.length - 1}
								>
									Next
									<ChevronRight className="h-4 w-4 ml-2" />
								</Button>
							</div>
						</CardContent>
					</Card>
				</div>
			</div>
		</div>
	);
}
