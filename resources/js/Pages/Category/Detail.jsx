import { useState } from "react";
import { UserCheck, UserPlus, Tag } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Card, CardContent } from "@/Components/Ui/Card";
import { Badge } from "@/Components/Ui/Badge";
import { Link } from "@inertiajs/react";
import { useAuthStore } from "@/Stores/AuthStore";
import ApiService from "@/Services/ApiService";
import Feed from "@/Components/Content/Feed";
import { toast } from "@/Hooks/useToast";
import { useTranslation } from "react-i18next";

export default function TagDetailPage({ category, profile, articles, news, tags }) {
	const [isFollowing, setIsFollowing] = useState(category.isFollowing);
	const [followers, setFollowers] = useState(Number(category.followers));
	const authStore = useAuthStore();
	const { t } = useTranslation();

	const handleFollow = async () => {
		ApiService.fetchJson(route("api.react.follow", { type: "category", slug: category.slug }))
			.then(() => {
				setIsFollowing(!isFollowing);
				setFollowers(isFollowing ? followers - 1 : followers + 1);
			})
			.catch(() => {
				toast({
					title: t("Error"),
					description: t("You can react to the same category once a day"),
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
							<Tag className="w-8 h-8"></Tag>
						</div>
						<div className="flex-1">
							<h1 className="text-3xl font-bold mb-2">{category.name}</h1>
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
			<Feed hasNavigation hasSidebar filters={{ category: category.slug }}></Feed>
		</div>
	);
}
