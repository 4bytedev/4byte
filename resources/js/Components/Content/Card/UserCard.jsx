import { Avatar, AvatarImage, AvatarFallback } from "@/Components/Ui/Avatar";
import { useEffect, useState } from "react";
import { Card, CardContent } from "../../Ui/Card";
import { Trans, useTranslation } from "react-i18next";
import { useAuthStore } from "@/Stores/AuthStore";
import { Button } from "../../Ui/Form/Button";
import { toast } from "@/Hooks/useToast";
import { Settings, UserCheck, UserPlus } from "lucide-react";
import { Link } from "@inertiajs/react";
import { useMutation } from "@tanstack/react-query";
import UserApi from "@/Api/UserApi";
import ReactApi from "@/Api/ReactApi";

export function UserCard({ username }) {
	const [isFollowing, setIsFollowing] = useState(false);
	const [followers, setFollowers] = useState(0);
	const [user, setUser] = useState({});
	const [profile, setProfile] = useState({});
	const authStore = useAuthStore();
	const { t } = useTranslation();

	const isOwnProfile = authStore.isAuthenticated && user.username === authStore.user.username;

	const previewMutation = useMutation({
		mutationFn: () => UserApi.preview({ username }),
		onSuccess: (response) => {
			setUser(response.user);
			setProfile(response.profile);
			setFollowers(Number(response.user.followers));
			setIsFollowing(response.user.isFollowing);
		},
	});

	useEffect(() => {
		previewMutation.mutate();
	}, []);

	const followMutation = useMutation({
		mutationFn: () => ReactApi.follow({ type: "user", slug: user.username }),
		onSuccess: () => {
			setIsFollowing(!isFollowing);
			setFollowers(isFollowing ? followers - 1 : followers + 1);
		},
		onError: () => {
			toast({
				title: t("Error"),
				description: t("You can react to the same user once a day"),
				variant: "destructive",
			});
		},
	});

	const handleFollow = async () => {
		followMutation.mutate();
	};

	if (previewMutation.isPending) {
		return (
			<div className="flex justify-center py-8">
				<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
			</div>
		);
	}

	return (
		<Card className="mb-8 relative h-64 mb-48 md:mb-24">
			<img
				src={profile.cover.image || "/img/wallpaper-dark.jpg"}
				srcSet={profile.cover.srcset}
				alt="Cover"
				className="absolute inset-0 w-full h-full object-cover object-center"
			/>
			<Button className="absolute right-1 top-1" variant="outline" asChild>
				<Link className="flex" href={route("user.view", { username })}>
					<Settings className="h-4 w-4 mr-2" />
					{t("View Profile")}
				</Link>
			</Button>
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
						</div>
					</div>
				</div>
			</CardContent>
		</Card>
	);
}
