import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Mail, ArrowRight, CheckCircle } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { Form, FormField } from "@/Components/Ui/Form/Form";
import { useModalStore } from "@/Stores/ModalStore";
import { useSiteStore } from "@/Stores/SiteStore";
import { useTranslation } from "react-i18next";
import { useState } from "react";
import { forgotPasswordSchema } from "@/Validation/AuthValidation";
import AuthApi from "@/Api/AuthApi";
import { useMutation } from "@tanstack/react-query";
import { FormInput } from "@/Components/Ui/Form/FormInput";

export default function ForgotPasswordModal() {
	const modalStore = useModalStore();
	const siteStore = useSiteStore();
	const { t } = useTranslation();
	const [isEmailSent, setIsEmailSent] = useState(false);

	const form = useForm({
		resolver: zodResolver(forgotPasswordSchema(t)),
		defaultValues: {
			email: "",
		},
	});

	const forgotPasswordMutation = useMutation({
		mutationFn: (data) => AuthApi.forgotPassword(data),
		onSuccess: () => {
			setIsEmailSent(true);
		},
		onError: (response) => {
			form.setError("email", {
				message: response.errors?.email || t("Invalid credentials. Please try again."),
			});
		},
	});

	const onSubmit = (data) => {
		forgotPasswordMutation.mutate(data);
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

						<Form form={form} onSubmit={onSubmit} className="space-y-4">
							<FormField
								control={form.control}
								name="email"
								render={({ field }) => (
									<FormInput
										icon={Mail}
										label={t("Email")}
										placeholder={t("Enter your email")}
										field={field}
									/>
								)}
							/>

							<Button type="submit" className="w-full">
								<div className="flex items-center space-x-2 justify-center">
									<span>{t("Send Reset Email")}</span>
									<ArrowRight className="h-4 w-4" />
								</div>
							</Button>
						</Form>

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
