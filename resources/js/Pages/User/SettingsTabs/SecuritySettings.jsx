import UserApi from "@/Api/UserApi";
import PasswordConfirmationModal from "@/Components/Modals/Auth/PasswordConfirmationModal";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Button } from "@/Components/Ui/Form/Button";
import { Form, FormField } from "@/Components/Ui/Form/Form";
import { FormPasswordInput } from "@/Components/Ui/Form/FormPasswordInput";
import { Label } from "@/Components/Ui/Form/Label";
import { TabsContent } from "@/Components/Ui/Tabs";
import { useAuthStore } from "@/Stores/AuthStore";
import { securitySettingsSchema } from "@/Validation/UserValidation";
import { zodResolver } from "@hookform/resolvers/zod";
import { router } from "@inertiajs/react";
import { useMutation } from "@tanstack/react-query";
import { Monitor, Smartphone, Trash2 } from "lucide-react";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";

export function SecuritySettings({ sessions: initialSessions }) {
	const { t } = useTranslation();
	const authStore = useAuthStore();
	const [sessions, setSessions] = useState(initialSessions);
	const [isConfirPasswordModelOpen, setIsConfirPasswordModelOpen] = useState(false);
	const [isDeleteAccountModelOpen, setIsDeleteAccountModelOpen] = useState(false);

	const form = useForm({
		resolver: zodResolver(securitySettingsSchema(t)),
		defaultValues: {
			current_password: "",
			new_password: "",
			new_password_confirmation: "",
		},
	});

	const changePasswordMutation = useMutation({
		mutationFn: (data) => UserApi.changePassword(data),
		onSuccess: () => {
			authStore.logout();
			router.visit("/", {
				method: "get",
			});
		},
		onError: (error) => {
			if (error?.errors) {
				Object.keys(error.errors).forEach((key) => {
					form.setError(key, { message: error.errors[key][0] });
				});
			} else {
				form.setError("email", { message: t("Invalid credentials. Please try again.") });
			}
		},
	});

	const onChangePasswordSubmit = (data) => {
		changePasswordMutation.mutate(data);
	};

	const onLogOutOtherBrowserSessionsSubmit = () => {
		setSessions((prev) => prev.filter((p) => p.is_current_device));
	};

	const onDeleteAccountSubmit = () => {
		authStore.logout();
		router.visit("/", {
			method: "get",
		});
	};

	return (
		<>
			<TabsContent value="security" className="space-y-6">
				<Form form={form} onSubmit={onChangePasswordSubmit} className="space-y-4">
					<Card>
						<CardHeader>
							<CardTitle>{t("Change Password")}</CardTitle>
						</CardHeader>
						<CardContent className="space-y-4">
							<FormField
								control={form.control}
								name="current_password"
								render={({ field }) => (
									<FormPasswordInput
										placeholder=""
										canShowPassword={false}
										label={t("Current Password")}
										field={field}
									/>
								)}
							/>
							<FormField
								control={form.control}
								name="new_password"
								render={({ field }) => (
									<FormPasswordInput
										placeholder=""
										passwordStrength={form.watch("new_password")}
										label={t("New Password")}
										field={field}
									/>
								)}
							/>
							<FormField
								control={form.control}
								name="new_password_confirmation"
								render={({ field }) => (
									<FormPasswordInput
										placeholder=""
										label={t("Confirm New Password")}
										field={field}
									/>
								)}
							/>
							<Button type="submit">{t("Update Password")}</Button>
						</CardContent>
					</Card>
				</Form>

				<Card>
					<CardHeader>
						<CardTitle className="flex justify-between">
							{t("Sessions")}
							{sessions.length > 1 && (
								<Button
									variant="ghost"
									onClick={() => setIsConfirPasswordModelOpen(true)}
								>
									{t("Log Out Other Browser Sessions")}
								</Button>
							)}
						</CardTitle>
					</CardHeader>
					<CardContent className="space-y-4">
						{sessions.map((session, index) => (
							<div key={index} className="flex items-center">
								<div>
									{session.device.desktop ? (
										<Monitor className="w-8 h-8 text-gray-500 dark:text-gray-400" />
									) : (
										<Smartphone className="w-8 h-8 text-gray-500 dark:text-gray-400" />
									)}
								</div>

								<div className="ms-3">
									<div className="text-sm text-gray-600 dark:text-gray-400">
										{session.device.platform || t("Unknown")} -{" "}
										{session.device.browser || t("Unknown")}
									</div>

									<div className="text-xs text-gray-500">
										{session.ip_address},{" "}
										{session.is_current_device ? (
											<span className="font-semibold text-primary-500">
												{t("Current Device")}
											</span>
										) : (
											<>
												{t("Last Active")} {session.last_active}
											</>
										)}
									</div>
								</div>
							</div>
						))}
					</CardContent>
				</Card>

				<Card className="border-destructive">
					<CardHeader>
						<CardTitle className="text-destructive">{t("Danger Zone")}</CardTitle>
					</CardHeader>
					<CardContent className="space-y-4">
						<div className="flex items-center justify-between">
							<div>
								<Label className="text-destructive">{t("Delete Account")}</Label>
								<p className="text-sm text-muted-foreground">
									{t("Permanently delete your account and all associated data")}
								</p>
							</div>
							<Button
								onClick={() => setIsDeleteAccountModelOpen(true)}
								variant="destructive"
							>
								<Trash2 className="h-4 w-4 mr-2" />
								{t("Delete Account")}
							</Button>
						</div>
					</CardContent>
				</Card>
			</TabsContent>

			<PasswordConfirmationModal
				open={isConfirPasswordModelOpen}
				setOpen={setIsConfirPasswordModelOpen}
				onSubmit={UserApi.logOutOtherBrowserSessions}
				onSuccess={onLogOutOtherBrowserSessionsSubmit}
			/>
			<PasswordConfirmationModal
				open={isDeleteAccountModelOpen}
				setOpen={setIsDeleteAccountModelOpen}
				onSubmit={UserApi.deleteAccount}
				onSuccess={onDeleteAccountSubmit}
			/>
		</>
	);
}
