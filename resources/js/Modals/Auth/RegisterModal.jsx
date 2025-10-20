import { useState } from "react";
import { Eye, EyeOff, Mail, Lock, ArrowRight, User, Fingerprint } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Input } from "@/Components/Ui/Input";
import { Label } from "@/Components/Ui/Label";
import { Checkbox } from "@/Components/Ui/Checkbox";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { useSiteStore } from "@/Stores/SiteStore";
import { Link, router } from "@inertiajs/react";
import ApiService from "@/Services/ApiService";
import { useAuthStore } from "@/Stores/AuthStore";
import { useModalStore } from "@/Stores/ModalStore";
import { Trans, useTranslation } from "react-i18next";
import Validation from "@/Data/Validation";

export default function RegisterModal() {
	const [formData, setFormData] = useState({
		name: "",
		email: "",
		username: "",
		password: "",
		password_confirmation: "",
		termsAccepted: false,
	});

	const [showPassword, setShowPassword] = useState(false);
	const [showConfirmPassword, setShowConfirmPassword] = useState(false);
	const [isLoading, setIsLoading] = useState(false);
	const [errors, setErrors] = useState({});
	const siteStore = useSiteStore();
	const authStore = useAuthStore();
	const modalStore = useModalStore();
	const { t } = useTranslation();

	const handleInputChange = (field, value) => {
		setFormData((prev) => ({ ...prev, [field]: value }));
		if (errors[field]) {
			setErrors((prev) => ({ ...prev, [field]: "" }));
		}
	};

	const validateForm = () => {
		const newErrors = {};
		const passwordRegex = Validation.password;
		const emailRegex = Validation.email;
		const usernameRegex = Validation.username;

		if (!formData.name.trim()) newErrors.name = t("Full name is required");
		if (!formData.email) newErrors.email = t("Email is required");
		else if (!emailRegex.test(formData.email)) newErrors.email = t("Invalid email format");
		if (!formData.username.trim()) newErrors.username = t("Username is required");
		else if (!usernameRegex.test(formData.username))
			newErrors.username = t("Invalid username format");
		if (!formData.password) newErrors.password = t("Password is required");
		else if (formData.password.length < 8)
			newErrors.password = t("Password must be at least 8 characters");
		else if (!passwordRegex.test(formData.password))
			newErrors.password = t(
				"Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.",
			);
		if (!formData.password_confirmation)
			newErrors.password_confirmation = t("Password confirmation is required");
		else if (formData.password !== formData.password_confirmation)
			newErrors.password_confirmation = t("Passwords do not match");
		if (!formData.termsAccepted)
			newErrors.termsAccepted = t("You must accept the Terms of Service to continue.");

		setErrors(newErrors);
		return Object.keys(newErrors).length === 0;
	};

	const handleSubmit = (e) => {
		e.preventDefault();
		if (!validateForm()) return;
		setIsLoading(true);
		setErrors({});

		ApiService.fetchJson(route("api.auth.register"), formData, { method: "POST" })
			.then((response) => {
				authStore.setUser(response);
				setIsLoading(false);
				if (siteStore.settings.verification) {
					router.visit(route("user.verification-view"), {
						method: "get",
					});
				}
				modalStore.close("register");
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
				setIsLoading(false);
			});
	};

	const getPasswordStrength = () => {
		const password = formData.password;
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
		<Modal open={modalStore.register} onOpenChange={modalStore.toggle("register")}>
			<ModalContent>
				<ModalTitle className="text-center mb-2 text-2xl">
					{t("Create a New Account")}
				</ModalTitle>
				<ModalDescription className="text-center mb-6">
					{t("Join our community by filling out your information below")}
				</ModalDescription>

				<form onSubmit={handleSubmit} className="space-y-4">
					<div className="space-y-2">
						<Label htmlFor="name">{t("Full Name")}</Label>
						<div className="relative">
							<User className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
							<Input
								id="name"
								type="text"
								placeholder={t("Enter your full name")}
								value={formData.name}
								onChange={(e) => handleInputChange("name", e.target.value)}
								className={`pl-10 ${errors.name ? "border-red-500" : ""}`}
							/>
						</div>
						{errors.name && <p className="text-sm text-red-500">{errors.name}</p>}
					</div>

					<div className="space-y-2">
						<Label htmlFor="email">{t("Email")}</Label>
						<div className="relative">
							<Mail className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
							<Input
								id="email"
								type="email"
								placeholder={t("Enter your email")}
								value={formData.email}
								onChange={(e) => handleInputChange("email", e.target.value)}
								className={`pl-10 ${errors.email ? "border-red-500" : ""}`}
							/>
						</div>
						{errors.email && <p className="text-sm text-red-500">{errors.email}</p>}
					</div>

					<div className="space-y-2">
						<Label htmlFor="username">{t("Username")}</Label>
						<div className="relative">
							<Fingerprint className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
							<Input
								id="username"
								type="text"
								placeholder={t("Enter your username")}
								value={formData.username}
								onChange={(e) => handleInputChange("username", e.target.value)}
								className={`pl-10 ${errors.username ? "border-red-500" : ""}`}
							/>
						</div>
						{errors.username && (
							<p className="text-sm text-red-500">{errors.username}</p>
						)}
					</div>

					<div className="space-y-2">
						<Label htmlFor="password">{t("Password")}</Label>
						<div className="relative">
							<Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
							<Input
								id="password"
								type={showPassword ? "text" : "password"}
								placeholder={t("Enter your password")}
								value={formData.password}
								onChange={(e) => handleInputChange("password", e.target.value)}
								className={`pl-10 pr-10 ${errors.password ? "border-red-500" : ""}`}
							/>
							<Button
								type="button"
								variant="ghost"
								size="icon"
								className="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8"
								onClick={() => setShowPassword(!showPassword)}
							>
								{showPassword ? (
									<EyeOff className="h-4 w-4" />
								) : (
									<Eye className="h-4 w-4" />
								)}
							</Button>
						</div>
						{formData.password && (
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
										style={{ width: `${passwordStrength.percentage}%` }}
									></div>
								</div>
							</div>
						)}
						{errors.password && (
							<p className="text-sm text-red-500">{errors.password}</p>
						)}
					</div>

					<div className="space-y-2">
						<Label htmlFor="password_confirmation">{t("Confirm Password")}</Label>
						<div className="relative">
							<Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
							<Input
								id="password_confirmation"
								type={showConfirmPassword ? "text" : "password"}
								placeholder={t("Confirm your password")}
								value={formData.password_confirmation}
								onChange={(e) =>
									handleInputChange("password_confirmation", e.target.value)
								}
								className={`pl-10 pr-10 ${errors.password_confirmation ? "border-red-500" : ""}`}
							/>
							<Button
								type="button"
								variant="ghost"
								size="icon"
								className="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8"
								onClick={() => setShowConfirmPassword(!showConfirmPassword)}
							>
								{showConfirmPassword ? (
									<EyeOff className="h-4 w-4" />
								) : (
									<Eye className="h-4 w-4" />
								)}
							</Button>
						</div>
						{errors.password_confirmation && (
							<p className="text-sm text-red-500">{errors.password_confirmation}</p>
						)}
					</div>

					<div className="flex items-start space-x-2">
						<Checkbox
							id="terms"
							checked={formData.termsAccepted}
							onCheckedChange={(checked) =>
								handleInputChange("termsAccepted", checked)
							}
						/>
						<div className="grid gap-1.5 leading-none">
							<label htmlFor="terms" className="text-sm font-medium">
								<Trans
									i18nKey="agreement"
									components={{
										termsLink: (
											<Link
												href={route("page.view", {
													slug: siteStore.pages.terms,
												})}
												className="text-primary hover:underline"
											/>
										),
										privacyLink: (
											<Link
												href={route("page.view", {
													slug: siteStore.pages.privacy,
												})}
												className="text-primary hover:underline"
											/>
										),
									}}
								/>
							</label>
							{errors.termsAccepted && (
								<p className="text-sm text-red-500">{errors.termsAccepted}</p>
							)}
						</div>
					</div>

					<Button type="submit" className="w-full" disabled={isLoading}>
						{isLoading ? (
							<div className="flex items-center space-x-2 justify-center">
								<div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
								<span>{t("Creating Account...")}</span>
							</div>
						) : (
							<div className="flex items-center space-x-2 justify-center">
								<span>{t("Create My Account")}</span>
								<ArrowRight className="h-4 w-4" />
							</div>
						)}
					</Button>
				</form>

				{siteStore.settings.login && (
					<div className="text-center text-sm mt-4">
						<span>{t("Already have an account?")} </span>
						<a
							href="#"
							onClick={modalStore.open("login")}
							className="text-primary hover:underline font-medium"
						>
							{t("Sign In")}
						</a>
					</div>
				)}
			</ModalContent>
		</Modal>
	);
}
