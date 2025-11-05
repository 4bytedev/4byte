import UserApi from "@/Api/UserApi";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Button } from "@/Components/Ui/Form/Button";
import {
	Form,
	FormControl,
	FormField,
	FormItem,
	FormLabel,
	FormMessage,
} from "@/Components/Ui/Form/Form";
import { FormInput } from "@/Components/Ui/Form/FormInput";
import { Input } from "@/Components/Ui/Form/Input";
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from "@/Components/Ui/Form/Select";
import { Textarea } from "@/Components/Ui/Form/Textarea";
import { TabsContent } from "@/Components/Ui/Tabs";
import { profileSettingsSchema } from "@/Validation/UserValidation";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Globe, Loader, Navigation, Plus, Save, X } from "lucide-react";
import { useState } from "react";
import { useFieldArray, useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";

export function ProfileSettings({ profile }) {
	const { t } = useTranslation();
	const [coverFile, setCoverFile] = useState(null);
	const [newSocial, setNewSocial] = useState("");

	const roleOptions = [
		"Frontend Developer",
		"Backend Developer",
		"Full-stack Developer",
		"Mobile Developer",
		"DevOps Engineer",
		"Data Scientist",
		"Machine Learning Engineer",
		"UI/UX Designer",
		"Product Manager",
		"Software Architect",
		"QA Engineer",
		"Security Engineer",
	];

	const form = useForm({
		resolver: zodResolver(profileSettingsSchema(t)),
		defaultValues: {
			bio: profile.bio || "",
			location: profile.location || "",
			website: profile.website || "",
			role: profile.role || "",
			socials: profile.socials || [],
			cover: profile.cover.image || "",
		},
	});

	const { fields, append, remove } = useFieldArray({
		control: form.control,
		name: "socials",
	});

	const profileSettingsMutation = useMutation({
		mutationFn: (data) => {
			const payload = {
				bio: data.bio,
				location: data.location,
				website: data.website,
				socials: data.socials,
				role: data.role,
				...(coverFile && { cover: coverFile }),
			};

			return UserApi.updateProfile(payload);
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
		profileSettingsMutation.mutate(data);
	};
	return (
		<TabsContent value="profile" className="space-y-6">
			<Form form={form} onSubmit={onSubmit} className="space-y-4">
				<Card>
					<CardHeader>
						<CardTitle>{t("Profile Information")}</CardTitle>
					</CardHeader>
					<CardContent className="space-y-6">
						<div
							className="mb-8 relative h-64 bg-no-repeat bg-cover bg-center bg-muted rounded-lg"
							style={{ backgroundImage: `url(${form.watch("cover")})` }}
						>
							<div className="absolute bottom-2 left-1/2 transform -translate-x-1/2 text-center">
								<Button variant="outline" size="sm" type="button">
									<label className="cursor-pointer" htmlFor="cover-input">
										{t("Change Cover")}
									</label>
								</Button>
								<p className="text-xs text-muted-foreground mt-1">
									{t("JPG, PNG or GIF. Max size 2MB.")}
								</p>
								<input
									hidden
									type="file"
									id="cover-input"
									accept="image/*"
									onChange={(e) => {
										const file = e.target.files[0];
										if (file) {
											setCoverFile(file);
											const previewUrl = URL.createObjectURL(file);
											form.setValue("cover", previewUrl);
										}
									}}
								/>
							</div>
						</div>
						{form.getFieldState("cover").error && (
							<p className="text-sm text-red-500">
								{form.getFieldState("cover").error}
							</p>
						)}
						<div className="space-y-2">
							<FormField
								control={form.control}
								name="role"
								render={({ field }) => (
									<FormItem>
										<FormLabel>{t("Role")}</FormLabel>
										<FormControl>
											<Select
												value={field.value}
												onValueChange={(val) => field.onChange(val)}
											>
												<SelectTrigger>
													<SelectValue
														placeholder={t("Select your role")}
													/>
												</SelectTrigger>
												<SelectContent>
													{roleOptions.map((role) => (
														<SelectItem key={role} value={role}>
															{role}
														</SelectItem>
													))}
												</SelectContent>
											</Select>
										</FormControl>
										<FormMessage />
									</FormItem>
								)}
							/>
						</div>

						<div className="space-y-2">
							<FormField
								control={form.control}
								name="bio"
								render={({ field }) => (
									<FormItem>
										<FormLabel>{t("Bio")}</FormLabel>
										<FormControl>
											<Textarea
												placeholder={t("Tell us about yourself...")}
												rows={3}
												{...field}
											/>
										</FormControl>
										<FormMessage />
									</FormItem>
								)}
							/>
						</div>

						<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
							<FormField
								control={form.control}
								name="location"
								render={({ field }) => (
									<FormInput
										icon={Navigation}
										label={t("Location")}
										field={field}
									/>
								)}
							/>
							<FormField
								control={form.control}
								name="website"
								render={({ field }) => (
									<FormInput icon={Globe} label={t("Website")} field={field} />
								)}
							/>
						</div>
					</CardContent>
				</Card>
				<Card>
					<CardHeader>
						<CardTitle>{t("Social Accounts")}</CardTitle>
						<p className="text-sm text-muted-foreground">
							{t("Add your social accounts and let people follow you")}
						</p>
					</CardHeader>
					<CardContent className="space-y-6">
						<div className="space-y-4">
							{fields.map((field, index) => (
								<div
									key={field.id}
									className="flex items-center justify-between p-4 border rounded-lg"
								>
									<div className="flex-1">
										<div className="flex items-center space-x-4 mb-2">
											<span className="font-medium">
												{form.getValues(`socials.${index}`)}
											</span>
										</div>
									</div>
									<Button
										variant="ghost"
										size="sm"
										type="button"
										onClick={() => remove(index)}
										className="text-red-500 hover:text-red-700"
									>
										<X className="h-4 w-4" />
									</Button>
								</div>
							))}
							{form.getFieldState("socials").error && (
								<p className="text-sm text-red-500">
									{form.getFieldState("socials").error}
								</p>
							)}
						</div>

						<div className="border-t pt-4">
							<div className="flex gap-4 items-end">
								<FormItem className="flex-1">
									<FormLabel>{t("Account Url")}</FormLabel>
									<FormControl>
										<Input
											value={newSocial}
											onChange={(e) => setNewSocial(e.target.value)}
											placeholder="https://..."
										/>
									</FormControl>
								</FormItem>
								<Button
									type="button"
									onClick={() => {
										if (newSocial.trim()) {
											append(newSocial.trim());
											setNewSocial("");
										}
									}}
									className="mt-2"
									disabled={!newSocial.length}
								>
									<Plus className="h-4 w-4 mr-2" />
									{t("Add Social")}
								</Button>
							</div>
						</div>
					</CardContent>
				</Card>
				<div className="flex justify-end mt-8">
					<Button type="submit" size="lg">
						{profileSettingsMutation.isPending ? (
							<Loader className="h-4 w-4 mr-2" />
						) : (
							<Save className="h-4 w-4 mr-2" />
						)}
						{profileSettingsMutation.isSuccess ? t("Changes Saved") : t("Save Changes")}
					</Button>
				</div>
			</Form>
		</TabsContent>
	);
}
