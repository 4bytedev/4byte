import { useState } from "react";
import { Hash, UserCheck, UserPlus } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Card, CardContent } from "@/Components/Ui/Card";
import { Badge } from "@/Components/Ui/Badge";
import { Link } from "@inertiajs/react";
import { useAuthStore } from "@/Stores/AuthStore";
import ApiService from "@/Services/ApiService";
import Feed from "@/Components/Layout/Feed";
import { toast } from "@/Hooks/useToast";
import { useTranslation } from "react-i18next";

export default function TagDetailPage({ tag, profile, articles, news, tags }) {
	console.log(tags);

	const [isFollowing, setIsFollowing] = useState(tag.isFollowing);
	const [followers, setFollowers] = useState(Number(tag.followers));
	const authStore = useAuthStore();
	const { t } = useTranslation();

	const handleFollow = async () => {
		ApiService.fetchJson(route("api.react.follow", { type: "tag", slug: tag.slug }))
			.then(() => {
				setIsFollowing(!isFollowing);
				setFollowers(isFollowing ? followers - 1 : followers + 1);
			})
			.catch(() => {
				toast({
					title: t("Error"),
					description: t("You can react to the same tag once a day"),
					variant: "destructive",
				});
			});
	};

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-6xl mx-auto">
				<div className="mb-8">
					<div className="flex items-center space-x-4 mb-6">
						<div
							className="w-16 h-16 rounded-lg flex items-center justify-center text-white"
							style={{ backgroundColor: profile.color }}
						>
							<Hash className="h-8 w-8" />
						</div>
						<div className="flex-1">
							<h1 className="text-3xl font-bold mb-2">{tag.name}</h1>
							<p className="text-muted-foreground text-lg">{profile.description}</p>
						</div>
						<div className="text-right">
							{authStore.isAuthenticated && (
								<Button
									onClick={handleFollow}
									variant={isFollowing ? "outline" : "default"}
								>
									{isFollowing ? (
										<>
											<UserCheck className="h-4 w-4 mr-2" />
											Following
										</>
									) : (
										<>
											<UserPlus className="h-4 w-4 mr-2" />
											Follow
										</>
									)}
								</Button>
							)}
						</div>
					</div>

					<div className="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
						<Card>
							<CardContent className="p-4 text-center">
								<div className="text-2xl font-bold">{articles}</div>
								<div className="text-sm text-muted-foreground">Articles</div>
							</CardContent>
						</Card>
						<Card>
							<CardContent className="p-4 text-center">
								<div className="text-2xl font-bold">0</div>
								<div className="text-sm text-muted-foreground">Events</div>
							</CardContent>
						</Card>
						<Card>
							<CardContent className="p-4 text-center">
								<div className="text-2xl font-bold">0</div>
								<div className="text-sm text-muted-foreground">Tutorials</div>
							</CardContent>
						</Card>
						<Card>
							<CardContent className="p-4 text-center">
								<div className="text-2xl font-bold">{news}</div>
								<div className="text-sm text-muted-foreground">News</div>
							</CardContent>
						</Card>
						<Card>
							<CardContent className="p-4 text-center">
								<div className="text-2xl font-bold">
									{Number(articles) + Number(news)}
								</div>
								<div className="text-sm text-muted-foreground">Usage</div>
							</CardContent>
						</Card>
						<Card>
							<CardContent className="p-4 text-center">
								<div className="text-2xl font-bold">{followers}</div>
								<div className="text-sm text-muted-foreground">Followers</div>
							</CardContent>
						</Card>
					</div>

					<div className="mb-6">
						<h3 className="text-sm font-medium mb-3">Related Tags</h3>
						<div className="flex flex-wrap gap-2">
							{profile.categories.map((category) => (
								<Link
									key={category.slug}
									href={route("category.view", { slug: category.slug })}
								>
									<Badge
										key={category.slug}
										variant="outline"
										className="cursor-pointer hover:bg-muted"
									>
										{category.name}
									</Badge>
								</Link>
							))}
							{tags.map((tag) => (
								<Link key={tag.slug} href={route("tag.view", { slug: tag.slug })}>
									<Badge
										variant="outline"
										className="cursor-pointer hover:bg-muted"
									>
										{tag.name}
									</Badge>
								</Link>
							))}
						</div>
					</div>
				</div>
			</div>
			<Feed hasNavigation hasSidebar filters={{ tag: tag.slug }}></Feed>
		</div>
	);
}
