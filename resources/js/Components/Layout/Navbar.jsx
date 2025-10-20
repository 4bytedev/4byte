import React, { useState } from "react";
import { Link } from "@inertiajs/react";
import { Search, User, Settings, LogOut } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Input } from "@/Components/Ui/Input";
import {
	DropdownMenu,
	DropdownMenuContent,
	DropdownMenuItem,
	DropdownMenuSeparator,
	DropdownMenuTrigger,
} from "@/Components/Ui/DropdownMenu";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { ThemeToggle } from "@/Components/Ui/ThemeToggle";
import { NotificationDropdown } from "@/Components/Ui/NotificationDropdown";
import ApiService from "@/Services/ApiService";
import { useAuthStore } from "@/Stores/AuthStore";
import { useSiteStore } from "@/Stores/SiteStore";
import { useModalStore } from "@/Stores/ModalStore";
import { useTranslation } from "react-i18next";

export function Navbar() {
	const [searchQuery, setSearchQuery] = useState("");
	const authStore = useAuthStore();
	const siteStore = useSiteStore();
	const modalStore = useModalStore();
	const { t } = useTranslation();

	const logout = () => {
		ApiService.fetchJson(route("api.auth.logout"), {}, { method: "POST" });
		authStore.logout();
	};

	return (
		<nav className="sticky top-0 z-50 border-b bg-background/80 backdrop-blur-sm">
			<div className="container mx-auto px-4">
				<div className="flex h-16 items-center justify-between">
					{/* Logo */}
					<Link href="/" className="flex items-center space-x-2">
						{siteStore.getLogo() ? (
							<img src={siteStore.getLogo()} alt={siteStore.title} className="h-8" />
						) : (
							<span className="font-bold text-xl">{siteStore.title}</span>
						)}
					</Link>

					{/* Search Bar */}
					<div className="flex items-center space-x-4 flex-1 max-w-md mx-2 md:mx-8 hidden md:block">
						<div className="relative flex-1">
							<Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4" />
							<Input
								placeholder="Search articles, news, users ..."
								value={searchQuery}
								onChange={(e) => setSearchQuery(e.target.value)}
								className="pl-10"
							/>
						</div>
					</div>

					{/* Right Actions */}
					<div className="flex items-center space-x-2 md:space-x-4">
						<ThemeToggle />

						{authStore.isAuthenticated ? (
							<>
								<NotificationDropdown />

								{/* User Menu */}
								<DropdownMenu>
									<DropdownMenuTrigger asChild>
										<Button variant="ghost" size="icon">
											<Avatar className="h-8 w-8">
												<AvatarImage
													src={
														authStore.user.avatar ||
														"/placeholder-avatar.jpg"
													}
													alt={authStore.user.name || "User"}
												/>
												<AvatarFallback>
													{authStore.user.name
														.split(" ")
														.map((n) => n[0])
														.join("") || "U"}
												</AvatarFallback>
											</Avatar>
										</Button>
									</DropdownMenuTrigger>
									<DropdownMenuContent align="end" className="w-48">
										<div className="px-2 py-1.5 text-sm flex">
											<Avatar className="h-10 w-10 me-2">
												<AvatarImage
													src={
														authStore.user.avatar ||
														"/placeholder-avatar.jpg"
													}
													alt={authStore.user.name || "User"}
												/>
												<AvatarFallback>
													{authStore.user.name
														.split(" ")
														.map((n) => n[0])
														.join("") || "U"}
												</AvatarFallback>
											</Avatar>
											<div>
												<div className="font-medium">
													{authStore.user.name}
												</div>
												<div className="text-muted-foreground">
													@{authStore.user.username}
												</div>
											</div>
										</div>
										<DropdownMenuSeparator />
										<DropdownMenuItem asChild>
											<Link
												className="flex w-full h-full"
												href={route("user.view", {
													username: authStore.user.username,
												})}
											>
												<User className="mr-2 h-4 w-4" />
												{t("Profile")}
											</Link>
										</DropdownMenuItem>
										<DropdownMenuItem asChild>
											<Link
												className="flex w-full h-full"
												href={route("user.settings.view")}
											>
												<Settings className="mr-2 h-4 w-4" />
												{t("Settings")}
											</Link>
										</DropdownMenuItem>
										<DropdownMenuSeparator />
										<DropdownMenuItem onClick={logout}>
											<LogOut className="mr-2 h-4 w-4" />
											{t("Logout")}
										</DropdownMenuItem>
									</DropdownMenuContent>
								</DropdownMenu>
							</>
						) : (
							<div className="flex items-center space-x-1 md:space-x-2">
								{siteStore.settings.login && (
									<Button variant="ghost" onClick={modalStore.open("login")}>
										{t("Login")}
									</Button>
								)}
								{siteStore.settings.register && (
									<Button onClick={modalStore.open("register")}>
										{t("Register")}
									</Button>
								)}
							</div>
						)}
					</div>
				</div>
			</div>
		</nav>
	);
}
