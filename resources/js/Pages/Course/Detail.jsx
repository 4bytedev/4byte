import { Clock, Users, Star, BookOpen, Circle, FileText, Code, Video } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Form/Button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Badge } from "@/Components/Ui/Badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Components/Ui/Tabs";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";

export default function TutorialPage() {
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

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-6xl mx-auto">
				<div className="grid lg:grid-cols-3 gap-8 mb-8">
					<div className="lg:col-span-2">
						<div className="flex items-center space-x-2 text-sm text-muted-foreground mb-4">
							<Badge className={getDifficultyColor(tutorial.difficulty)}>
								{tutorial.difficulty}
							</Badge>
							{tutorial.tags.map((tag) => (
								<Badge key={tag} variant="outline">
									{tag}
								</Badge>
							))}
						</div>

						<h1 className="text-3xl font-bold mb-4">{tutorial.title}</h1>
						<p className="text-muted-foreground text-lg mb-6">{tutorial.description}</p>

						<div className="flex items-center space-x-6 text-sm">
							<div className="flex items-center space-x-1">
								<Clock className="h-4 w-4 text-muted-foreground" />
								<span>{tutorial.duration}</span>
							</div>
							<div className="flex items-center space-x-1">
								<BookOpen className="h-4 w-4 text-muted-foreground" />
								<span>{tutorial.totalLessons} lessons</span>
							</div>
							<div className="flex items-center space-x-1">
								<Users className="h-4 w-4 text-muted-foreground" />
								<span>{tutorial.enrolled.toLocaleString()} enrolled</span>
							</div>
							<div className="flex items-center space-x-1">
								<Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
								<span>
									{tutorial.rating} ({tutorial.reviews} reviews)
								</span>
							</div>
						</div>
					</div>

					<div className="space-y-4">
						<Card>
							<CardContent className="p-6">
								<UserProfileHover user={tutorial.instructor}>
									<div className="flex items-center space-x-3 cursor-pointer mb-4">
										<Avatar className="h-12 w-12">
											<AvatarImage
												src={tutorial.instructor.avatar}
												alt={tutorial.instructor.name}
											/>
											<AvatarFallback>
												{tutorial.instructor.name
													.split(" ")
													.map((n) => n[0])
													.join("")}
											</AvatarFallback>
										</Avatar>
										<div>
											<p className="font-medium">
												{tutorial.instructor.name}
											</p>
											<p className="text-sm text-muted-foreground">
												{tutorial.instructor.role}
											</p>
										</div>
									</div>
								</UserProfileHover>

								<div className="text-center">
									<div className="text-2xl font-bold mb-2">{tutorial.price}</div>
									<Button
										className="w-full"
										size="lg"
										onClick={() => console.log("Start Tutorial")}
									>
										{"Start Course"}
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
						<div className="prose prose-lg dark:prose-invert max-w-none">
							<h2>What You&apos;ll Learn</h2>
							<ul>
								<li>Fundamentals of React and component-based architecture</li>
								<li>Modern React hooks and state management</li>
								<li>Building interactive user interfaces</li>
								<li>Performance optimization techniques</li>
								<li>Testing React applications</li>
								<li>Deployment and production best practices</li>
							</ul>

							<h2>Prerequisites</h2>
							<ul>
								<li>Basic knowledge of HTML, CSS, and JavaScript</li>
								<li>Familiarity with ES6+ features</li>
								<li>Understanding of web development concepts</li>
							</ul>
						</div>
					</TabsContent>

					<TabsContent value="curriculum" className="mt-6">
						<div className="space-y-4">
							{tutorial.chapters.map((chapter, chapterIndex) => (
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
														<span className="font-medium">
															{lesson.title}
														</span>
														<Badge
															variant="outline"
															className="text-xs capitalize"
														>
															{getLessonTypeLabel(lesson.type)}
														</Badge>
													</div>
													<span className="text-sm text-muted-foreground">
														{lesson.duration}
													</span>
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
