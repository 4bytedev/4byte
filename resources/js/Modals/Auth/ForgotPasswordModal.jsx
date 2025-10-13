import { useState } from "react";
import { Mail, ArrowRight, CheckCircle } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Input } from "@/Components/Ui/Input";
import { Label } from "@/Components/Ui/Label";
import ApiService from "@/Services/ApiService";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { useModalStore } from "@/Stores/ModalStore";
import { useSiteStore } from "@/Stores/SiteStore";
import { useTranslation } from "react-i18next";
import Validation from "@/Data/Validation";

export default function ForgotPasswordModal() {
	const [formData, setFormData] = useState({
		email: "",
	});
	const [isEmailSent, setIsEmailSent] = useState(false);
	const [isLoading, setIsLoading] = useState(false);
	const [errors, setErrors] = useState({});
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

		if (!formData.email) {
			newErrors.email = t("Email is required");
		} else if (!emailRegex.test(formData.email)) {
			newErrors.email = t("Invalid email format");
		}

		setErrors(newErrors);
		return Object.keys(newErrors).length === 0;
	};

	const handleSubmit = (e) => {
		e.preventDefault();
		if (!validateForm()) return;
		setIsLoading(true);
		setErrors({});

		ApiService.fetchJson(route("api.auth.forgot-password"), formData, { method: "POST" })
			.then(() => {
				setIsLoading(false);
				setIsEmailSent(true);
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
				setIsLoading(false);
			});
	};

	return (
		<Modal open={modalStore.forgotPassword} onOpenChange={modalStore.toggle("forgotPassword")}>
			<ModalContent>
				{isEmailSent ? (
					<>
						<ModalTitle className="text-center mb-2 text-2xl">
							{t("Check your email")}
						</ModalTitle>
						<ModalDescription className="text-center text-gray-500 mb-6">
							{t("We've sent a password reset email")}
						</ModalDescription>

						<div className="mx-auto mb-4 h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
							<CheckCircle className="h-8 w-8 text-green-600 dark:text-green-400" />
						</div>

						<div className="text-center space-y-4">
							<p className="text-sm text-muted-foreground">
								{t(
									"Didn't receive the email? Check your spam folder or try again.",
								)}
							</p>
						</div>

						{siteStore.settings.login && (
							<div className="text-center text-sm mt-4">
								<a
									href="#!"
									onClick={modalStore.open("login")}
									className="text-primary hover:underline font-medium"
								>
									{t("Back to login")}
								</a>
							</div>
						)}
					</>
				) : (
					<>
						<ModalTitle className="text-center mb-2 text-2xl">
							{t("Forgot your password?")}
						</ModalTitle>
						<ModalDescription className="text-center text-gray-500 mb-6">
							{t("No worries! Enter your email and we'll send you a reset link.")}
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
								{errors.email && (
									<p className="text-sm text-red-500">{errors.email}</p>
								)}
							</div>

							<Button type="submit" className="w-full" disabled={isLoading}>
								{isLoading ? (
									<div className="flex items-center space-x-2 justify-center">
										<div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
										<span>{t("Loading...")}</span>
									</div>
								) : (
									<div className="flex items-center space-x-2 justify-center">
										<span>{t("Send Reset Email")}</span>
										<ArrowRight className="h-4 w-4" />
									</div>
								)}
							</Button>
						</form>

						{siteStore.settings.login && (
							<div className="text-center text-sm mt-4">
								<a
									href="#!"
									onClick={modalStore.open("login")}
									className="text-primary hover:underline font-medium"
								>
									{t("Back to login")}
								</a>
							</div>
						)}
					</>
				)}
			</ModalContent>
		</Modal>
	);
}
