import { useEffect, useState } from "react";
import { Bell, Check, X } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import {
	DropdownMenu,
	DropdownMenuContent,
	DropdownMenuTrigger,
	DropdownMenuSeparator,
} from "@/Components/Ui/DropdownMenu";
import { Badge } from "@/Components/Ui/Badge";
import { ScrollArea } from "@/Components/Ui/ScrollArea";
import ApiService from "@/Services/ApiService";
import { useTranslation } from "react-i18next";

export function NotificationDropdown() {
	const [notifications, setNotifications] = useState([]);
	const [notificationsCount, setNotificationsCount] = useState(0);
	const [isLoading, setIsLoading] = useState(true);
	const [hasFetched, setHasFetched] = useState(false);
	const { t } = useTranslation();

	useEffect(() => {
		ApiService.fetchJson(route("api.notification.count"), {}, { method: "GET" }).then(
			(response) => {
				setNotificationsCount(response.count);
			},
		);
	}, []);

	const markAsRead = (id) => {
		const notification = notifications.find((n) => n.id === id);
		if (!notification || notification.read_at) {
			return;
		}
		ApiService.fetchJson(route("api.notification.mark-as-read"), { id });
		setNotifications((prev) =>
			prev.map((n) => (n.id === id ? { ...n, read_at: new Date().toISOString() } : n)),
		);
		setNotificationsCount(notificationsCount - 1);
	};

	const markAllAsRead = () => {
		if (notifications.every((n) => n.read_at)) return;
		ApiService.fetchJson(route("api.notification.mark-all-as-read"));
		setNotifications((prev) => prev.map((n) => ({ ...n, read_at: new Date().toISOString() })));
		setNotificationsCount(0);
	};

	const removeNotification = (id) => {
		setNotifications((prev) => prev.filter((n) => n.id !== id));
	};

	const getNotificationIcon = (status) => {
		switch (status) {
			case "success":
				return <Bell className="h-4 w-4 text-green-600" />;
			case "warning":
				return <Bell className="h-4 w-4 text-yellow-500" />;
			case "danger":
				return <Bell className="h-4 w-4 text-red-500" />;
			default:
				return <Bell className="h-4 w-4 text-gray-600" />;
		}
	};

	const formatTimeAgo = (timestamp) => {
		const date = new Date(timestamp);
		const now = new Date();
		const diffInMinutes = Math.floor((now.getTime() - date.getTime()) / (1000 * 60));

		if (diffInMinutes < 1) return t("Just now");
		if (diffInMinutes < 60) return `${diffInMinutes}${t("m ago")}`;
		const diffInHours = Math.floor(diffInMinutes / 60);
		if (diffInHours < 24) return `${diffInHours}${t("h ago")}`;
		const diffInDays = Math.floor(diffInHours / 24);
		return `${diffInDays}${t("d ago")}`;
	};

	const handleTriggerClick = () => {
		if (hasFetched) return;
		setIsLoading(true);

		ApiService.fetchJson(route("api.notification.list"), {}, { method: "GET" }).then(
			(response) => {
				setNotifications(response);
				setIsLoading(false);
				setHasFetched(true);
			},
		);
	};

	return (
		<DropdownMenu onClick={handleTriggerClick}>
			<DropdownMenuTrigger asChild>
				<Button variant="ghost" size="icon" className="relative">
					<Bell className="h-5 w-5" />
					{notificationsCount > 0 && (
						<Badge className="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center p-0 text-xs">
							{notificationsCount > 9 ? "9+" : notificationsCount}
						</Badge>
					)}
				</Button>
			</DropdownMenuTrigger>
			<DropdownMenuContent align="end" className="w-80 z-75">
				<div className="flex items-center justify-between p-4">
					<h3 className="font-semibold">{t("Notifications")}</h3>
					<div className="flex items-center space-x-2">
						{notificationsCount > 0 && (
							<Button variant="ghost" size="sm" onClick={markAllAsRead}>
								<Check className="h-4 w-4 mr-1" />
								{t("Mark all read")}
							</Button>
						)}
					</div>
				</div>
				<DropdownMenuSeparator />
				<ScrollArea className="h-96">
					{isLoading && (
						<div className="flex justify-center py-8">
							<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
						</div>
					)}
					{hasFetched && (
						<>
							{notifications.length === 0 ? (
								<div className="p-4 text-center text-muted-foreground">
									<Bell className="h-8 w-8 mx-auto mb-2 opacity-50" />
									<p>{t("No notifications")}</p>
								</div>
							) : (
								<div className="space-y-1">
									{notifications.map((notification) => (
										<div
											key={notification.id}
											className={`p-3 hover:bg-muted/50 cursor-pointer border-l-2 ${
												notification.read_at
													? "border-l-transparent"
													: "border-l-primary bg-primary/5"
											}`}
										>
											<div className="flex items-start space-x-3">
												<div className="flex-shrink-0 mt-1">
													{getNotificationIcon(notification.data.status)}
												</div>
												<div className="flex-1 min-w-0">
													<div className="flex items-center justify-between">
														<p className="text-sm font-medium truncate">
															{notification.data.title}
														</p>
														<Button
															variant="ghost"
															size="icon"
															className="h-6 w-6 opacity-0 group-hover:opacity-100"
															onClick={(e) => {
																e.stopPropagation();
																removeNotification(notification.id);
															}}
														>
															<X className="h-3 w-3" />
														</Button>
													</div>
													<p className="text-xs text-muted-foreground mt-1">
														{notification.data.body}
													</p>
													{notification.data.actions?.length > 0 && (
														<div className="mt-2 flex flex-wrap gap-2">
															{notification.data.actions.map(
																(action, i) => (
																	<Button
																		key={i}
																		size="sm"
																		variant="outline"
																		onClick={(e) => {
																			e.stopPropagation();

																			if (
																				action.shouldMarkAsRead
																			)
																				markAsRead(
																					notification.id,
																				);

																			if (action.url) {
																				window.open(
																					action.url,
																					action.shouldOpenUrlInNewTab
																						? "_blank"
																						: "_self",
																				);
																			}
																		}}
																	>
																		{action.label}
																	</Button>
																),
															)}
														</div>
													)}
													<p className="text-xs text-muted-foreground mt-1">
														{formatTimeAgo(notification.created_at)}
													</p>
												</div>
											</div>
										</div>
									))}
								</div>
							)}
						</>
					)}
				</ScrollArea>
			</DropdownMenuContent>
		</DropdownMenu>
	);
}
