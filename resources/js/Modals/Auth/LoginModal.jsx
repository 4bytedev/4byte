import { useState } from "react";
import { Eye, EyeOff, Mail, Lock, ArrowRight } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Input } from "@/Components/Ui/Input";
import { Label } from "@/Components/Ui/Label";
import { Checkbox } from "@/Components/Ui/Checkbox";
import ApiService from "@/Services/ApiService";
import { useAuthStore } from "@/Stores/AuthStore";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { useModalStore } from "@/Stores/ModalStore";
import { useSiteStore } from "@/Stores/SiteStore";
import { useTranslation } from "react-i18next";
import Validation from "@/Data/Validation";

export default function LoginModal() {
	const [formData, setFormData] = useState({
		email: "",
		password: "",
		rememberMe: false,
	});
	const [showPassword, setShowPassword] = useState(false);
	const [isLoading, setIsLoading] = useState(false);
	const [errors, setErrors] = useState({});
	const authStore = useAuthStore();
	const siteStore = useSiteStore();
	const modalStore = useModalStore();
	const { t } = useTranslation();

	const handleInputChange = (field, value) => {
		setFormData((prev) => ({ ...prev, [field]: value }));
		if (errors[field]) setErrors((prev) => ({ ...prev, [field]: "" }));
	};

	const validateForm = () => {
		const newErrors = {};
		const emailRegex = Validation.email;
		const passwordRegex = Validation.password;

		if (!formData.email) {
			newErrors.email = t("Email is required");
		} else if (!emailRegex.test(formData.email)) {
			newErrors.email = t("Invalid email format");
		}

		if (!formData.password) {
			newErrors.password = t("Password is required");
		} else if (formData.password.length < 8)
			newErrors.email = t("These credentials do not match our records.");
		else if (!passwordRegex.test(formData.password))
			newErrors.email = t("These credentials do not match our records.");

		setErrors(newErrors);
		return Object.keys(newErrors).length === 0;
	};

	const handleSubmit = (e) => {
		e.preventDefault();
		if (!validateForm()) return;
		setIsLoading(true);
		setErrors({});

		ApiService.fetchJson(route("api.auth.login"), formData, { method: "POST" })
			.then((response) => {
				authStore.setUser(response);
				setIsLoading(false);
				modalStore.close("login");
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
				setIsLoading(false);
			});
	};

	return (
		<Modal open={modalStore.login} onOpenChange={modalStore.toggle("login")}>
			<ModalContent>
				<ModalTitle className="text-center mb-2 text-2xl">{t("Sign In")}</ModalTitle>
				<ModalDescription className="text-center text-gray-500 mb-6">
					{t("Enter your credentials to access your account")}
				</ModalDescription>

				<form onSubmit={handleSubmit} className="space-y-4">
					{errors.general && (
						<div className="p-3 rounded-md bg-red-50 border border-red-200">
							<p className="text-sm text-red-600">{errors.general}</p>
						</div>
					)}

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
						{errors.password && (
							<p className="text-sm text-red-500">{errors.password}</p>
						)}
					</div>

					<div className="flex items-center justify-between">
						<div className="flex items-center space-x-2">
							<Checkbox
								id="rememberMe"
								checked={formData.rememberMe}
								onCheckedChange={(checked) =>
									handleInputChange("rememberMe", checked)
								}
							/>
							<Label htmlFor="rememberMe" className="text-sm">
								{t("Remember me")}
							</Label>
						</div>
						<a
							href="#!"
							onClick={modalStore.open("forgotPassword")}
							className="text-sm text-primary hover:underline"
						>
							{t("Forgot Password?")}
						</a>
					</div>

					<Button type="submit" className="w-full" disabled={isLoading}>
						{isLoading ? (
							<div className="flex items-center space-x-2 justify-center">
								<div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
								<span>{t("Loading...")}</span>
							</div>
						) : (
							<div className="flex items-center space-x-2 justify-center">
								<span>{t("Sign In")}</span>
								<ArrowRight className="h-4 w-4" />
							</div>
						)}
					</Button>
				</form>

				{siteStore.settings.register && (
					<div className="text-center text-sm mt-4">
						<span>{t("Don't have an account?")} </span>
						<a
							href="#!"
							onClick={modalStore.open("register")}
							className="text-primary hover:underline font-medium"
						>
							{t("Sign Up")}
						</a>
					</div>
				)}
			</ModalContent>
		</Modal>
	);
}
