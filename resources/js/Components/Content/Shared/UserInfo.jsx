import { Avatar, AvatarImage, AvatarFallback } from "@/Components/Ui/Avatar";
import { UserProfileHover } from "@/Components/Ui/UserProfileHover";

export function UserInfo({ user }) {
	if (!user) return null;

	return (
		<UserProfileHover username={user.username}>
			<div className="flex items-center space-x-2">
				<div className="px-2 py-1.5 text-sm flex">
					<Avatar className="h-10 w-10 me-2">
						<AvatarImage
							src={user.avatar || "/placeholder-avatar.jpg"}
							alt={user.name || "User"}
						/>
						<AvatarFallback>
							{user.name
								.split(" ")
								.map((n) => n[0])
								.join("") || "U"}
						</AvatarFallback>
					</Avatar>
					<div>
						<div className="font-medium">{user.name}</div>
						<div className="text-muted-foreground">@{user.username}</div>
					</div>
				</div>
			</div>
		</UserProfileHover>
	);
}
