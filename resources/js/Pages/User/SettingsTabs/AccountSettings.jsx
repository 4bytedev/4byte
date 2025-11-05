import UserApi from "@/Api/UserApi";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Button } from "@/Components/Ui/Form/Button";
import { Form, FormField } from "@/Components/Ui/Form/Form";
import { FormInput } from "@/Components/Ui/Form/FormInput";
import { Label } from "@/Components/Ui/Form/Label";
import { TabsContent } from "@/Components/Ui/Tabs";
import { useAuthStore } from "@/Stores/AuthStore";
import { accountSettingsSchema } from "@/Validation/UserValidation";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Fingerprint, Loader, Mail, Save, User } from "lucide-react";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";

export function AccountSettings({ account }) {
	const { t } = useTranslation();
	const authStore = useAuthStore();
	const [avatarFile, setAvatarFile] = useState(null);

	const form = useForm({
		resolver: zodResolver(accountSettingsSchema(t)),
		defaultValues: {
			avatar: account.avatar,
			name: account.name,
			username: account.username,
			email: account.email,
		},
	});

	const accountSettingsMutation = useMutation({
		mutationFn: (data) => {
			const payload = {
				name: data.name,
				username: data.username,
				email: data.email,
				...(avatarFile && { avatar: avatarFile }),
			};

			return UserApi.updateAccount(payload);
		},
		onSuccess: () => {
			authStore.updateUser({
				name: form.getValues("name"),
				avatar: form.getValues("avatar"),
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

	const onSubmit = (data) => {
		accountSettingsMutation.mutate(data);
	};

	return (
		<TabsContent value="account" className="space-y-6">
			<Form form={form} onSubmit={onSubmit} className="space-y-4">
				<Card>
					<CardHeader>
						<CardTitle>{t("Account Information")}</CardTitle>
					</CardHeader>
					<CardContent className="space-y-6">
						<div className="flex items-center space-x-4">
							<Avatar className="h-20 w-20">
								<AvatarImage src={form.watch("avatar")} alt="Profile" />
								<AvatarFallback className="text-lg">
									{form
										.watch("name")
										.split(" ")
										.map((n) => n[0])
										.join("") || "U"}
								</AvatarFallback>
							</Avatar>
							<div>
								<Button variant="outline" size="sm" type="button">
									<Label className="cursor-pointer" htmlFor="avatar-input">
										{t("Change Avatar")}
									</Label>
								</Button>
								<p className="text-xs text-muted-foreground mt-1">
									{t("JPG, PNG or GIF. Max size 2MB.")}
								</p>
								<input
									hidden
									type="file"
									id="avatar-input"
									accept="image/*"
									onChange={(e) => {
										const file = e.target.files[0];
										if (file) {
											setAvatarFile(file);
											const previewUrl = URL.createObjectURL(file);
											form.setValue("avatar", previewUrl);
										}
									}}
								/>
							</div>
							{form.getFieldState("avatar").error && (
								<p className="text-sm text-red-500">
									{form.getFieldState("avatar").error}
								</p>
							)}
						</div>

						<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
							<FormField
								control={form.control}
								name="name"
								render={({ field }) => (
									<FormInput icon={User} label={t("Full Name")} field={field} />
								)}
							/>
							<FormField
								control={form.control}
								name="username"
								render={({ field }) => (
									<FormInput
										icon={Fingerprint}
										label={t("Username")}
										disabled
										field={field}
									/>
								)}
							/>
						</div>

						<FormField
							control={form.control}
							name="email"
							render={({ field }) => (
								<FormInput icon={Mail} label={t("Email")} field={field} disabled />
							)}
						/>
					</CardContent>
				</Card>
				<div className="flex justify-end mt-8">
					<Button type="submit" size="lg">
						{accountSettingsMutation.isPending ? (
							<Loader className="h-4 w-4 mr-2" />
						) : (
							<Save className="h-4 w-4 mr-2" />
						)}
						{accountSettingsMutation.isSuccess ? t("Changes Saved") : t("Save Changes")}
					</Button>
				</div>
			</Form>
		</TabsContent>
	);
}
