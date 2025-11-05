import { Link } from "@inertiajs/react";
import { UserInfo } from "../Shared/UserInfo";

export function UserPreviewCard({ name, username, avatar }) {
	return (
		<Link
			href={route("user.view", {
				username: username,
			})}
		>
			<div
				key={username}
				className="rounded-lg hover:bg-accent/50 cursor-pointer transition-colors"
			>
				<UserInfo name={name} username={username} avatar={avatar} />
			</div>
		</Link>
	);
}
