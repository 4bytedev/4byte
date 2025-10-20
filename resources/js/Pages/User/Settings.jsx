import { useEffect, useState } from "react";
import {
	User,
	Palette,
	Shield,
	Trash2,
	Save,
	Eye,
	EyeOff,
	Plus,
	X,
	Settings,
	Loader,
	Monitor,
	Smartphone,
} from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Input } from "@/Components/Ui/Input";
import { Label } from "@/Components/Ui/Label";
import { Textarea } from "@/Components/Ui/Textarea";
import { Switch } from "@/Components/Ui/Switch";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Components/Ui/Tabs";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from "@/Components/Ui/Select";
import { useTheme } from "@/Contexts/ThemeContext";
import ApiService from "@/Services/ApiService";
import { useAuthStore } from "@/Stores/AuthStore";
import { router } from "@inertiajs/react";
import { Trans, useTranslation } from "react-i18next";
import PasswordConfirmationModal from "@/Modals/Auth/PasswordConfirmationModal";
import Validation from "@/Data/Validation";

export default function SettingsPage({ account, profile, sessions: initialSessions }) {
	const { theme, toggleTheme } = useTheme();
	const [errors, setErrors] = useState({});
	const [isLoading, setIsLoading] = useState(false);
	const [showPassword, setShowPassword] = useState(false);
	const authStore = useAuthStore();
	const { t } = useTranslation();
	const [isSaved, setIsSaved] = useState(false);
	const [isConfirPasswordModelOpen, setIsConfirPasswordModelOpen] = useState(false);
	const [isDeleteAccountModelOpen, setIsDeleteAccountModelOpen] = useState(false);
	const [sessions, setSessions] = useState(initialSessions);
	const [formData, setFormData] = useState({
		name: account.name,
		username: account.username,
		email: account.email,
		avatar: account.avatar,
		bio: profile.bio || "",
		location: profile.location || "",
		website: profile.website || "",
		role: profile.role || "",
		socials: profile.socials || [],
		cover: profile.cover.image || "",
		current_password: "",
		new_password: "",
		new_password_confirmation: "",
	});

	const [avatar, setAvatar] = useState(null);
	const [cover, setCover] = useState(null);
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

	const handleInputChange = (field, value) => {
		setFormData((prev) => ({ ...prev, [field]: value }));
	};

	useEffect(() => {
		if (isSaved) {
			const timer = setTimeout(() => {
				setIsSaved(false);
			}, 2000);

			return () => clearTimeout(timer);
		}
	}, [isSaved]);

	const handleAccountSubmit = (e) => {
		e.preventDefault();
		setIsLoading(true);
		setIsSaved(false);
		setErrors({});

		const data = { name: formData.name };
		if (avatar) {
			data.avatar = avatar;
		}
		ApiService.fetchJson(route("api.user.settings.account"), data, {
			method: "POST",
			isMultipart: true,
		})
			.then(() => {
				authStore.updateUser({ name: formData.name, avatar: formData.avatar });
				setIsLoading(false);
				setIsSaved(true);
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
				setIsLoading(false);
			});
	};

	const handleProfileSubmit = (e) => {
		e.preventDefault();
		setIsLoading(true);
		setIsSaved(false);
		setErrors({});

		const data = {
			role: formData.role,
			bio: formData.bio,
			location: formData.location,
			website: formData.website,
		};
		if (cover) {
			data.cover = cover;
		}
		if (formData.socials && formData.socials.length > 0) {
			formData.socials.forEach((social, index) => {
				data[`socials[${index}]`] = social;
			});
		}

		ApiService.fetchJson(route("api.user.settings.profile"), data, {
			method: "POST",
			isMultipart: true,
		})
			.then(() => {
				setIsSaved(true);
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
			})
			.finally(() => {
				setIsLoading(false);
			});
	};

	const validateChangePassword = () => {
		const newErrors = {};
		const passwordRegex = Validation.password;

		if (!formData.current_password) newErrors.current_password = t("Password is required");
		else if (formData.current_password.length < 8)
			newErrors.current_password = t("Invalid credential");
		else if (!passwordRegex.test(formData.current_password))
			newErrors.current_password = t("Invalid credential");

		if (!formData.new_password) newErrors.new_password = t("Password is required");
		else if (formData.new_password.length < 8)
			newErrors.new_password = t("Password must be at least 8 characters");
		else if (!passwordRegex.test(formData.new_password))
			newErrors.new_password = t(
				"Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.",
			);

		if (!formData.new_password_confirmation)
			newErrors.new_password_confirmation = t("Password confirmation is required");
		else if (formData.new_password_confirmation !== formData.new_password_confirmation)
			newErrors.new_password_confirmation = t("Passwords do not match");

		setErrors(newErrors);
		return Object.keys(newErrors).length === 0;
	};

	const handleChangePassword = (e) => {
		e.preventDefault();
		setIsLoading(true);
		setErrors({});

		if (!validateChangePassword()) return;

		const data = {
			current_password: formData.current_password,
			new_password: formData.new_password,
			new_password_confirmation: formData.new_password_confirmation,
		};

		ApiService.fetchJson(route("api.user.settings.password"), data)
			.then(() => {
				setIsLoading(false);
				authStore.logout();
				router.visit("/", {
					method: "get",
				});
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
				setIsLoading(false);
			});
	};

	const handleLogOutOtherBrowserSessions = (password) => {
		return ApiService.fetchJson(route("api.user.settings.logout-other-sessions"), { password });
	};

	const successLogOutOtherBrowserSessions = () => {
		setSessions((prev) => prev.filter((p) => p.is_current_device));
	};

	const handleDeleteAccount = (password) => {
		return ApiService.fetchJson(route("api.user.settings.delete-account"), { password });
	};

	const successDeleteAccount = () => {
		authStore.logout();
		router.visit("/", {
			method: "get",
		});
	};

	const getPasswordStrength = () => {
		const password = formData.new_password;
		if (!password) return { strength: 0, label: "" };

		let strength = 0;
		if (password.length >= 8) strength++;
		if (/[a-z]/.test(password)) strength++;
		if (/[A-Z]/.test(password)) strength++;
		if (/\d/.test(password)) strength++;
		if (/[^a-zA-Z\d]/.test(password)) strength++;

		const labels = [t("Very Weak"), t("Weak"), t("Fair"), t("Good"), t("Strong")];
		const colors = [
			"bg-red-500",
			"bg-orange-500",
			"bg-yellow-500",
			"bg-blue-500",
			"bg-green-500",
		];

		return {
			strength,
			label: labels[strength - 1] || "",
			color: colors[strength - 1] || "bg-gray-300",
			percentage: (strength / 5) * 100,
		};
	};

	const passwordStrength = getPasswordStrength();

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-4xl mx-auto">
				<div className="mb-8">
					<h1 className="text-3xl font-bold mb-2">{t("Settings")}</h1>
					<p className="text-muted-foreground">
						{t("Manage your account settings and preferences")}
					</p>
				</div>

				<Tabs defaultValue="account" className="w-full">
					<TabsList className="grid w-full grid-cols-4">
						<TabsTrigger value="account">
							<Settings className="h-4 w-4 mr-2" />
							{t("Account")}
						</TabsTrigger>
						<TabsTrigger value="profile">
							<User className="h-4 w-4 mr-2" />
							{t("Profile")}
						</TabsTrigger>
						<TabsTrigger value="appearance">
							<Palette className="h-4 w-4 mr-2" />
							{t("Appearance")}
						</TabsTrigger>
						<TabsTrigger value="security">
							<Shield className="h-4 w-4 mr-2" />
							{t("Security")}
						</TabsTrigger>
					</TabsList>

					{/* Account Settings */}
					<TabsContent value="account" className="space-y-6">
						<Card>
							<CardHeader>
								<CardTitle>{t("Account Information")}</CardTitle>
							</CardHeader>
							<CardContent className="space-y-6">
								{/* Avatar */}
								<div className="flex items-center space-x-4">
									<Avatar className="h-20 w-20">
										<AvatarImage src={formData.avatar} alt="Profile" />
										<AvatarFallback className="text-lg">
											{formData.name
												.split(" ")
												.map((n) => n[0])
												.join("") || "U"}
										</AvatarFallback>
									</Avatar>
									<div>
										<Button variant="outline" size="sm">
											<label
												className="cursor-pointer"
												htmlFor="avatar-input"
											>
												{t("Change Avatar")}
											</label>
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
													setAvatar(file);

													const previewUrl = URL.createObjectURL(file);
													handleInputChange("avatar", previewUrl);
												}
											}}
										/>
									</div>
									{errors.avatar && (
										<p className="text-sm text-red-500">{errors.avatar}</p>
									)}
								</div>

								<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
									<div className="space-y-2">
										<Label htmlFor="name">{t("Full Name")}</Label>
										<Input
											id="name"
											value={formData.name}
											onChange={(e) =>
												handleInputChange("name", e.target.value)
											}
										/>
										{errors.name && (
											<p className="text-sm text-red-500">{errors.name}</p>
										)}
									</div>
									<div className="space-y-2">
										<Label htmlFor="username">{t("Username")}</Label>
										<Input
											id="username"
											value={formData.username}
											onChange={(e) =>
												handleInputChange("username", e.target.value)
											}
											disabled
										/>
										{errors.username && (
											<p className="text-sm text-red-500">
												{errors.username}
											</p>
										)}
									</div>
								</div>

								<div className="space-y-2">
									<Label htmlFor="email">{t("Email")}</Label>
									<Input
										id="email"
										type="email"
										value={formData.email}
										onChange={(e) => handleInputChange("email", e.target.value)}
										disabled
									/>
									{errors.email && (
										<p className="text-sm text-red-500">{errors.email}</p>
									)}
								</div>
							</CardContent>
						</Card>
						{/* Save Button */}
						<div className="flex justify-end mt-8">
							<Button onClick={handleAccountSubmit} size="lg">
								{isLoading ? (
									<Loader className="h-4 w-4 mr-2" />
								) : (
									<Save className="h-4 w-4 mr-2" />
								)}
								{isSaved ? t("Changes Saved") : t("Save Changes")}
							</Button>
						</div>
					</TabsContent>

					<TabsContent value="profile" className="space-y-6">
						<Card>
							<CardHeader>
								<CardTitle>{t("Profile Information")}</CardTitle>
							</CardHeader>
							<CardContent className="space-y-6">
								<div
									className="mb-8 relative h-64 bg-no-repeat bg-cover bg-center bg-muted rounded-lg"
									style={{ backgroundImage: `url(${formData.cover})` }}
								>
									<div className="absolute bottom-2 left-1/2 transform -translate-x-1/2 text-center">
										<Button variant="outline" size="sm">
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
													setCover(file);

													const previewUrl = URL.createObjectURL(file);

													handleInputChange("cover", previewUrl);
												}
											}}
										/>
									</div>
								</div>
								{errors.cover && (
									<p className="text-sm text-red-500">{errors.cover}</p>
								)}
								<div className="space-y-2">
									<Label htmlFor="role">{t("Role")}</Label>
									<Select
										value={formData.role}
										onValueChange={(value) => handleInputChange("role", value)}
									>
										<SelectTrigger>
											<SelectValue placeholder={t("Select your role")} />
										</SelectTrigger>
										<SelectContent>
											{roleOptions.map((role) => (
												<SelectItem key={role} value={role}>
													{role}
												</SelectItem>
											))}
										</SelectContent>
									</Select>
									{errors.role && (
										<p className="text-sm text-red-500">{errors.role}</p>
									)}
								</div>

								<div className="space-y-2">
									<Label htmlFor="bio">{t("Bio")}</Label>
									<Textarea
										id="bio"
										placeholder={t("Tell us about yourself...")}
										value={formData.bio}
										onChange={(e) => handleInputChange("bio", e.target.value)}
										rows={3}
									/>
									{errors.bio && (
										<p className="text-sm text-red-500">{errors.bio}</p>
									)}
								</div>

								<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
									<div className="space-y-2">
										<Label htmlFor="location">{t("Location")}</Label>
										<Input
											id="location"
											value={formData.location}
											onChange={(e) =>
												handleInputChange("location", e.target.value)
											}
										/>
										{errors.location && (
											<p className="text-sm text-red-500">
												{errors.location}
											</p>
										)}
									</div>
									<div className="space-y-2">
										<Label htmlFor="website">{t("Website")}</Label>
										<Input
											id="website"
											value={formData.website}
											onChange={(e) =>
												handleInputChange("website", e.target.value)
											}
										/>
										{errors.website && (
											<p className="text-sm text-red-500">{errors.website}</p>
										)}
									</div>
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
								{/* Current Socials */}
								<div className="space-y-4">
									{formData.socials.map((social, index) => (
										<div
											key={index}
											className="flex items-center justify-between p-4 border rounded-lg"
										>
											<div className="flex-1">
												<div className="flex items-center space-x-4 mb-2">
													<span className="font-medium">{social}</span>
												</div>
											</div>
											<Button
												variant="ghost"
												size="sm"
												onClick={() =>
													setFormData({
														...formData,
														socials: formData.socials.filter(
															(_, i) => i !== index,
														),
													})
												}
												className="text-red-500 hover:text-red-700"
											>
												<X className="h-4 w-4" />
											</Button>
										</div>
									))}
								</div>

								{/* Add New Social */}
								<div className="border-t pt-4">
									<div className="flex gap-4 items-end">
										<div className="space-y-2 w-full">
											<Label>{t("Account Url")}</Label>
											<Input
												id="account-url"
												value={newSocial}
												onChange={(e) => setNewSocial(e.target.value)}
												className="w-full"
											/>
											{errors.socials && (
												<p className="text-sm text-red-500">
													{errors.socials}
												</p>
											)}
										</div>

										<Button
											disabled={!newSocial}
											onClick={() => {
												setFormData({
													...formData,
													socials: [...formData.socials, newSocial],
												});
												setNewSocial("");
											}}
										>
											<Plus className="h-4 w-4 mr-2" />
											{t("Add Social")}
										</Button>
									</div>
								</div>
							</CardContent>
						</Card>
						{/* Save Button */}
						<div className="flex justify-end mt-8">
							<Button onClick={handleProfileSubmit} size="lg">
								{isLoading ? (
									<Loader className="h-4 w-4 mr-2" />
								) : (
									<Save className="h-4 w-4 mr-2" />
								)}
								{isSaved ? t("Changes Saved") : t("Save Changes")}
							</Button>
						</div>
					</TabsContent>

					{/* Appearance */}
					<TabsContent value="appearance" className="space-y-6">
						<Card>
							<CardHeader>
								<CardTitle>{t("Theme")}</CardTitle>
							</CardHeader>
							<CardContent>
								<div className="flex items-center justify-between">
									<div>
										<Label>{t("Dark Mode")}</Label>
										<p className="text-sm text-muted-foreground">
											<Trans
												i18nKey="using_theme"
												values={{ theme: t(theme) }}
												components={{ strong: <strong /> }}
											/>
										</p>
									</div>
									<Switch
										checked={theme === "dark"}
										onCheckedChange={toggleTheme}
									/>
								</div>
							</CardContent>
						</Card>
					</TabsContent>

					{/* Security */}
					<TabsContent value="security" className="space-y-6">
						<Card>
							<CardHeader>
								<CardTitle>{t("Change Password")}</CardTitle>
							</CardHeader>
							<CardContent className="space-y-4">
								<div className="space-y-2">
									<Label htmlFor="current_password">
										{t("Current Password")}
									</Label>
									<div className="relative">
										<Input
											id="current_password"
											type={showPassword ? "text" : "password"}
											value={formData.current_password}
											onChange={(e) =>
												handleInputChange(
													"current_password",
													e.target.value,
												)
											}
										/>
										<Button
											type="button"
											variant="ghost"
											size="icon"
											className="absolute right-2 top-1/2 transform -translate-y-1/2"
											onClick={() => setShowPassword(!showPassword)}
										>
											{showPassword ? (
												<EyeOff className="h-4 w-4" />
											) : (
												<Eye className="h-4 w-4" />
											)}
										</Button>
									</div>
									{errors.current_password && (
										<p className="text-sm text-red-500">
											{errors.current_password}
										</p>
									)}
								</div>
								<div className="space-y-2">
									<Label htmlFor="new_password">{t("New Password")}</Label>
									<Input
										id="new_password"
										type="password"
										value={formData.new_password}
										onChange={(e) =>
											handleInputChange("new_password", e.target.value)
										}
									/>
									{formData.new_password && (
										<div className="space-y-2">
											<div className="flex items-center justify-between text-xs">
												<span className="text-muted-foreground">
													{t("Password Strength")}
												</span>
												<span
													className={`font-medium ${passwordStrength.strength >= 3 ? "text-green-600" : "text-orange-600"}`}
												>
													{passwordStrength.label}
												</span>
											</div>
											<div className="w-full bg-gray-200 rounded-full h-1.5">
												<div
													className={`h-1.5 rounded-full transition-all duration-300 ${passwordStrength.color}`}
													style={{
														width: `${passwordStrength.percentage}%`,
													}}
												></div>
											</div>
										</div>
									)}
									{errors.new_password && (
										<p className="text-sm text-red-500">
											{errors.new_password}
										</p>
									)}
								</div>
								<div className="space-y-2">
									<Label htmlFor="new_password_confirmation">
										{t("Confirm New Password")}
									</Label>
									<Input
										id="new_password_confirmation"
										type="password"
										value={formData.new_password_confirmation}
										onChange={(e) =>
											handleInputChange(
												"new_password_confirmation",
												e.target.value,
											)
										}
									/>
									{errors.new_password_confirmation && (
										<p className="text-sm text-red-500">
											{errors.new_password_confirmation}
										</p>
									)}
								</div>
								<Button onClick={handleChangePassword}>
									{t("Update Password")}
								</Button>
							</CardContent>
						</Card>

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
								<CardTitle className="text-destructive">
									{t("Danger Zone")}
								</CardTitle>
							</CardHeader>
							<CardContent className="space-y-4">
								<div className="flex items-center justify-between">
									<div>
										<Label className="text-destructive">
											{t("Delete Account")}
										</Label>
										<p className="text-sm text-muted-foreground">
											{t(
												"Permanently delete your account and all associated data",
											)}
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
				</Tabs>
			</div>
			<PasswordConfirmationModal
				open={isConfirPasswordModelOpen}
				setOpen={setIsConfirPasswordModelOpen}
				onSubmit={handleLogOutOtherBrowserSessions}
				onSuccess={successLogOutOtherBrowserSessions}
			/>
			<PasswordConfirmationModal
				open={isDeleteAccountModelOpen}
				setOpen={setIsDeleteAccountModelOpen}
				onSubmit={handleDeleteAccount}
				onSuccess={successDeleteAccount}
			/>
		</div>
	);
}
