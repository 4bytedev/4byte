import { Compass } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { useAuthStore } from "@/Stores/AuthStore";
import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";

export function Navigation() {
	const authStore = useAuthStore();
	const { t } = useTranslation();

	const navigations = [
		{
			title: t("Discover"),
			icon: Compass,
			href: route("home.view"),
		},
	];

	return (
		<aside className="w-full border-r bg-background/50 p-4 space-y-6">
			{/* Navigation Links */}
			<div className="space-y-2">
				{authStore.isAuthenticated && !authStore.user.verified && (
					<Link href={route("user.verification.view")}>
						<Button className="mb-2 whitespace-normal">
							{t("Verify Your Account")}
						</Button>
					</Link>
				)}
				<div className="space-y-1">
					{navigations.map((nav, index) => {
						const Icon = nav.icon;
						return (
							<Link
								href={nav.href}
								key={index}
								variant="ghost"
								className="w-full justify-start rounded-lg p-2 h-auto pointer flex hover:bg-accent hover:text-accent-foreground"
							>
								<Icon className="h-4 w-4 mr-2" />
								<span className="text-sm">{nav.title}</span>
							</Link>
						);
					})}
				</div>
			</div>
		</aside>
	);
}
