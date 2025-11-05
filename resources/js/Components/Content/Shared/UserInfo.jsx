import { Avatar, AvatarImage, AvatarFallback } from "@/Components/Ui/Avatar";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";

export function UserInfo({ name, username, avatar }) {
	return (
		<UserProfileHover username={username}>
			<div className="flex items-center space-x-2">
				<div className="px-2 py-1.5 text-sm flex">
					<Avatar className="h-10 w-10 me-2">
						<AvatarImage
							src={avatar || "/placeholder-avatar.jpg"}
							alt={name || "User"}
						/>
						<AvatarFallback>
							{name
								.split(" ")
								.map((n) => n[0])
								.join("") || "U"}
						</AvatarFallback>
					</Avatar>
					<div>
						<div className="font-medium">{name}</div>
						<div className="text-muted-foreground">@{username}</div>
					</div>
				</div>
			</div>
		</UserProfileHover>
	);
}
