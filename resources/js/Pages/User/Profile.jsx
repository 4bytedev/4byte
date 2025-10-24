import { useState } from "react";
import {
	Calendar,
	MapPin,
	Settings,
	UserPlus,
	UserCheck,
	Globe,
	LaptopMinimalCheck,
} from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Button";
import { Card, CardContent } from "@/Components/Ui/Card";
import { useAuthStore } from "@/Stores/AuthStore";
import { Link } from "@inertiajs/react";
import ApiService from "@/Services/ApiService";
import Feed from "@/Components/Layout/Feed";
import { Trans, useTranslation } from "react-i18next";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Components/Ui/Tabs";
import { toast } from "@/Hooks/useToast";
import { useMediaQuery } from "@uidotdev/usehooks";

export default function UserProfilePage({ user, profile }) {
	const [isFollowing, setIsFollowing] = useState(user.isFollowing);
	const [followers, setFollowers] = useState(Number(user.followers));
	const authStore = useAuthStore();
	const { t } = useTranslation();
	const isDesktop = useMediaQuery("(min-width: 1024px)");

	const isOwnProfile = authStore.isAuthenticated && user.username === authStore.user.username;

	const baseTabs = [
		{ label: t("All"), value: "all" },
		{ label: t("Articles"), value: "article" },
		{ label: t("News"), value: "news" },
	];

	const authTabs = isOwnProfile
		? [
				{ label: t("Saves"), value: "saves" },
				{ label: t("Drafts"), value: "drafts" },
			]
		: [];

	const tabs = [...baseTabs, ...authTabs];

	const handleFollow = async () => {
		ApiService.fetchJson(
			route("api.react.follow", { type: "user", slug: user.username }),
			{},
			{ method: "POST" },
		)
			.then(() => {
				setIsFollowing(!isFollowing);
				setFollowers(isFollowing ? followers - 1 : followers + 1);
			})
			.catch(() => {
				toast({
					title: t("Error"),
					description: t("You can react to the same user once a day"),
					variant: "destructive",
				});
			});
	};

	function ProfileSidebar({ user, profile }) {
		return (
			<div className="space-y-6">
				{profile.bio && <p>{profile.bio}</p>}
				{user.created_at && (
					<div className="flex">
						<Calendar className="h-5 w-5 mr-1" />
						{new Date(user.created_at).toLocaleDateString()}
					</div>
				)}
				{profile.role && (
					<div className="flex">
						<LaptopMinimalCheck className="h-5 w-5 mr-1" />
						<p>{profile.role}</p>
					</div>
				)}
				{profile.location && (
					<div className="flex">
						<MapPin className="h-5 w-5 mr-1" />
						{profile.location}
					</div>
				)}
				{profile.website && (
					<div className="flex">
						<Globe className="h-5 w-5 mr-1" />
						<a
							href={profile.website}
							target="_blank"
							rel="noopener noreferrer"
							className="hover:text-primary"
						>
							{profile.website}
						</a>
					</div>
				)}
			</div>
		);
	}

	if (!user) {
		return (
			<div className="container mx-auto px-4 py-8">
				<div className="max-w-4xl mx-auto text-center">
					<h1 className="text-2xl font-bold mb-4">{t("User not found")}</h1>
					<p className="text-muted-foreground">
						{t("The user you're looking for doesn't exist.")}
					</p>
				</div>
			</div>
		);
	}

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-4xl mx-auto mb-5">
				{/* Profile Header */}
				<Card className="mb-8 relative h-64">
					<img
						src={profile.cover.image || "/img/wallpaper-dark.jpg"}
						srcSet={profile.cover.srcset}
						alt="Cover"
						className="absolute inset-0 w-full h-full object-cover object-center"
					/>
					<CardContent className="ph-6 absolute -bottom-56 md:-bottom-32 left-0 w-full">
						<div className="flex flex-col md:flex-row md:items-start items-center md:items-center md:space-y-4 md:space-y-0 md:space-x-6 w-full">
							<div className="h-38 w-38 bg-background rounded-full p-3">
								<Avatar className="h-36 w-36">
									<AvatarImage src={user.avatar} alt={user.name} />
									<AvatarFallback className="text-4xl">
										{user.name
											.split(" ")
											.map((n) => n[0])
											.join("")}
									</AvatarFallback>
								</Avatar>
							</div>
							<div className="flex justify-between md:pt-6 w-full -pt-8 md:flex-row flex-col items-center">
								<div>
									<div className="flex items-center justify-center md:justify-start flex-wrap max-w-md">
										<h1 className="text-3xl font-bold">{user.name}</h1>
										<p className="text-muted-foreground ms-2 md:ms-0">
											@{user.username}
										</p>
									</div>
									<div className="flex-1">
										<div className="flex items-center space-x-6 text-sm justify-center md:justify-start">
											<span>
												<Trans
													i18nKey="followers"
													values={{ count: followers.toLocaleString() }}
													components={{ strong: <strong /> }}
												/>
											</span>
											<span>
												<Trans
													i18nKey="followings"
													values={{
														count: user.followings.toLocaleString(),
													}}
													components={{ strong: <strong /> }}
												/>
											</span>
										</div>
									</div>
								</div>
								<div className="pt-2">
									{!isOwnProfile && authStore.isAuthenticated && (
										<Button
											onClick={handleFollow}
											variant={isFollowing ? "outline" : "default"}
										>
											{isFollowing ? (
												<>
													<UserCheck className="h-4 w-4 mr-2" />
													{t("Following")}
												</>
											) : (
												<>
													<UserPlus className="h-4 w-4 mr-2" />
													{t("Follow")}
												</>
											)}
										</Button>
									)}
									{isOwnProfile && (
										<Button variant="outline" asChild>
											<Link
												className="flex"
												href={route("user.settings.view")}
											>
												<Settings className="h-4 w-4 mr-2" />
												{t("Edit Profile")}
											</Link>
										</Button>
									)}
								</div>
							</div>
						</div>
					</CardContent>
				</Card>
				<div className="grid grid-cols-10 gap-6 w-full md:mt-28 mt-56">
					{isDesktop && (
						<div className="col-span-3 hidden lg:block">
							<Card className="w-64 sticky top-20 border-r border-border">
								<CardContent className="p-6">
									<ProfileSidebar user={user} profile={profile} />
								</CardContent>
							</Card>
						</div>
					)}
					<div className="lg:col-span-7 col-span-10">
						{isDesktop ? (
							<Feed tabs={tabs} filters={{ user: user.username }} />
						) : (
							<div className="mb-4">
								<Tabs defaultValue="about">
									<TabsList className="w-full flex">
										<TabsTrigger value="about" className="flex-1">
											{t("About")}
										</TabsTrigger>
										<TabsTrigger value="feed" className="flex-1">
											{t("Feed")}
										</TabsTrigger>
									</TabsList>
									<TabsContent value="about">
										<Card className="border border-border">
											<CardContent className="p-6">
												<ProfileSidebar user={user} profile={profile} />
											</CardContent>
										</Card>
									</TabsContent>
									<TabsContent value="feed">
										<Feed tabs={tabs} filters={{ user: user.username }} />
									</TabsContent>
								</Tabs>
							</div>
						)}
					</div>
				</div>
			</div>
		</div>
	);
}
