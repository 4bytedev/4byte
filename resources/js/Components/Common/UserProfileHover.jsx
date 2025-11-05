import { useEffect, useRef, useState } from "react";
import { MapPin, Calendar, Users, ExternalLink, UserCheck, UserPlus, Settings } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Form/Button";
import { Card, CardContent } from "@/Components/Ui/Card";
import { HoverCard, HoverCardContent, HoverCardTrigger } from "@/Components/Ui/HoverCard";
import ApiService from "@/Services/ApiService";
import { Link } from "@inertiajs/react";
import { useAuthStore } from "@/Stores/AuthStore";
import { Trans, useTranslation } from "react-i18next";
import { toast } from "@/Hooks/useToast";

export function UserProfileHover({ username, children }) {
	const [isFollowing, setIsFollowing] = useState(false);
	const [followers, setFollowers] = useState(0);
	const [followings, setFollowings] = useState(0);
	const [user, setUser] = useState({});
	const [profile, setProfile] = useState({});
	const [isLoading, setIsLoading] = useState(true);
	const [hasFetched, setHasFetched] = useState(false);
	const authStore = useAuthStore();
	const [isOwnProfile, setIsOwnProfile] = useState(false);
	const hoverTimeoutRef = useRef(null);
	const { t } = useTranslation();

	const handleHover = () => {
		if (hasFetched) return;
		setIsLoading(true);

		hoverTimeoutRef.current = setTimeout(() => {
			setIsLoading(true);
			ApiService.fetchJson(
				route("api.user.preview", { username }),
				{},
				{ method: "GET" },
			).then((response) => {
				setUser(response.user);
				setProfile(response.profile);
				setFollowers(Number(response.user.followers));
				setFollowings(Number(response.user.followings));
				setIsFollowing(response.user.isFollowing);
				setIsLoading(false);
				setHasFetched(true);
			});
		}, 600);
	};

	const handleHoverEnd = () => {
		if (hoverTimeoutRef.current) {
			clearTimeout(hoverTimeoutRef.current);
			hoverTimeoutRef.current = null;
		}
	};

	useEffect(() => {
		return () => {
			if (hoverTimeoutRef.current) {
				clearTimeout(hoverTimeoutRef.current);
			}
		};
	}, []);

	useEffect(() => {
		setIsOwnProfile(authStore.isAuthenticated && user.username == authStore.user.username);
	}, [user]);

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

	return (
		<HoverCard>
			<HoverCardTrigger
				asChild
				onMouseEnter={handleHover}
				onMouseLeave={handleHoverEnd}
				onTouchStart={handleHover}
				onTouchEnd={handleHoverEnd}
			>
				{children}
			</HoverCardTrigger>
			<HoverCardContent className="w-80" side="bottom" align="start">
				<Card className="border-0 shadow-none">
					<CardContent className="p-4">
						{isLoading && (
							<div className="flex justify-center py-8">
								<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
							</div>
						)}
						{hasFetched && (
							<div className="flex items-start space-x-4">
								<div className="flex-1 space-y-2">
									<div className="flex justify-left">
										<Avatar className="h-16 w-16">
											<AvatarImage src={user.avatar} alt={user.name} />
											<AvatarFallback className="text-lg">
												{user.name
													.split(" ")
													.map((n) => n[0])
													.join("")}
											</AvatarFallback>
										</Avatar>
										<div className="ml-2">
											<h3 className="font-semibold text-lg">{user.name}</h3>
											<p className="text-sm text-muted-foreground">
												@{user.username}
											</p>
											<p className="text-sm text-muted-foreground">
												{profile.role}
											</p>
										</div>
									</div>

									{profile.bio && <p className="text-sm">{profile.bio}</p>}

									<div className="flex items-center space-x-4 text-xs text-muted-foreground">
										{profile.location && (
											<div className="flex items-center space-x-1">
												<MapPin className="h-3 w-3" />
												<span>{profile.location}</span>
											</div>
										)}
										<div className="flex items-center space-x-1">
											<Calendar className="h-3 w-3" />
											<span>
												<Trans
													i18nKey="joined"
													values={{
														date: new Date(
															user.created_at,
														).toLocaleDateString(),
													}}
													components={{ strong: <strong /> }}
												/>
											</span>
										</div>
									</div>

									<div className="flex items-center space-x-4 text-sm">
										<div className="flex items-center space-x-1">
											<Users className="h-4 w-4" />
											<span>
												<Trans
													i18nKey="followers"
													values={{ count: followers.toLocaleString() }}
													components={{ strong: <strong /> }}
												/>
											</span>
										</div>
										<span>•</span>
										<span>
											<Trans
												i18nKey="followings"
												values={{ count: followings.toLocaleString() }}
												components={{ strong: <strong /> }}
											/>
										</span>
									</div>

									<div className="flex items-center space-x-2 pt-2">
										{!isOwnProfile && authStore.isAuthenticated && (
											<Button
												size="sm"
												variant={isFollowing ? "outline" : "default"}
												onClick={handleFollow}
												className="flex"
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
										{isOwnProfile && authStore.isAuthenticated && (
											<Button size="sm" asChild>
												<Link
													className="flex"
													href={route("user.settings.view")}
												>
													<Settings className="h-4 w-4 mr-2" />
													{t("Edit Profile")}
												</Link>
											</Button>
										)}
										<Button size="sm" variant="outline" asChild>
											<Link
												className="flex"
												href={route("user.view", {
													username: user.username,
												})}
											>
												<ExternalLink className="h-4 w-4 mr-2" />
												{t("View Profile")}
											</Link>
										</Button>
									</div>
								</div>
							</div>
						)}
					</CardContent>
				</Card>
			</HoverCardContent>
		</HoverCard>
	);
}
