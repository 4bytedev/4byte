import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { ArrowRight, CheckCircle, AlertCircle, Mail } from "lucide-react";
import { useState, useEffect } from "react";
import { useModalStore } from "@/Stores/ModalStore";
import { resetPasswordSchema } from "@/Validation/AuthValidation";
import { useTranslation } from "react-i18next";
import AuthApi from "@/Api/AuthApi";
import { useMutation } from "@tanstack/react-query";
import { FormInput } from "@/Components/Ui/Form/FormInput";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Button } from "@/Components/Ui/Form/Button";
import { Form, FormField } from "@/Components/Ui/Form/Form";
import { FormPasswordInput } from "@/Components/Ui/Form/FormPasswordInput";

export default function ResetPasswordModal({ email, token }) {
	const { t } = useTranslation();
	const modalStore = useModalStore();
	const [tokenValid, setTokenValid] = useState(null);
	const [isSuccess, setIsSuccess] = useState(false);

	const form = useForm({
		resolver: zodResolver(resetPasswordSchema(t)),
		defaultValues: {
			token,
			email,
			password: "",
			password_confirmation: "",
		},
	});

	const resetPasswordMutation = useMutation({
		mutationFn: (data) => AuthApi.resetPassword(data),
		onSuccess: () => {
			setIsSuccess(true);
		},
		onError: (response) => {
			if (response?.errors) {
				Object.keys(response.errors).forEach((key) => {
					form.setError(key, { message: response.errors[key][0] });
				});
			} else {
				form.setError("email", { message: t("Invalid credentials. Please try again.") });
			}
		},
	});

	const onSubmit = (data) => {
		resetPasswordMutation.mutate(data);
	};

	useEffect(() => {
		const validateToken = async () => {
			if (!token) {
				setTokenValid(false);
				return;
			}

			try {
				await new Promise((resolve) => setTimeout(resolve, 1000));
				setTokenValid(true);
			} catch {
				setTokenValid(false);
			}
		};
		validateToken();
	}, [token]);

	if (tokenValid === null) {
		return (
			<div className="min-h-screen flex items-center justify-center p-4">
				<Card className="w-full max-w-md">
					<CardContent className="p-8 text-center">
						<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-4"></div>
						<p className="text-muted-foreground">{t("Validating reset link...")}</p>
					</CardContent>
				</Card>
			</div>
		);
	}

	if (tokenValid === false) {
		return (
			<div className="min-h-screen flex items-center justify-center p-4">
				<Card className="w-full max-w-md">
					<CardHeader className="text-center">
						<div className="mx-auto mb-4 h-16 w-16 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
							<AlertCircle className="h-8 w-8 text-red-600 dark:text-red-400" />
						</div>
						<CardTitle className="text-2xl font-bold">
							{t("Invalid reset link")}
						</CardTitle>
						<p className="text-muted-foreground">
							{t("This password reset link is invalid or has expired.")}
						</p>
					</CardHeader>
					<CardContent>
						<Button asChild className="w-full">
							<a href="#!" onClick={modalStore.open("forgotPassword")}>
								{t("Request new reset link")}
							</a>
						</Button>
						<div className="text-center mt-2">
							<a
								href="#!"
								onClick={modalStore.open("login")}
								className="text-sm text-primary hover:underline"
							>
								{t("Back to login")}
							</a>
						</div>
					</CardContent>
				</Card>
			</div>
		);
	}

	if (isSuccess) {
		return (
			<div className="min-h-screen flex items-center justify-center p-4">
				<Card className="w-full max-w-md">
					<CardHeader className="text-center">
						<div className="mx-auto mb-4 h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
							<CheckCircle className="h-8 w-8 text-green-600 dark:text-green-400" />
						</div>
						<CardTitle className="text-2xl font-bold">
							{t("Password reset successful")}
						</CardTitle>
						<p className="text-muted-foreground">
							{t("Your password has been successfully updated.")}
						</p>
					</CardHeader>
					<CardContent>
						<Button asChild className="w-full">
							<a href="#!" onClick={modalStore.open("login")}>
								<div className="flex items-center justify-center space-x-2">
									<span>{t("Sign in with new password")}</span>
									<ArrowRight className="h-4 w-4" />
								</div>
							</a>
						</Button>
					</CardContent>
				</Card>
			</div>
		);
	}

	return (
		<div className="min-h-screen flex items-center justify-center p-4">
			<Card className="w-full max-w-md">
				<CardHeader className="text-center">
					<CardTitle className="text-2xl font-bold">{t("Reset your password")}</CardTitle>
					<p className="text-muted-foreground">{t("Enter your new password below")}</p>
				</CardHeader>

				<CardContent>
					<Form form={form} onSubmit={onSubmit} className="space-y-4">
						<FormField
							control={form.control}
							name="token"
							render={({ field }) => (
								<FormInput field={field} disabled type="hidden" />
							)}
						/>

						<FormField
							control={form.control}
							name="email"
							render={({ field }) => (
								<FormInput
									icon={Mail}
									label={t("Email")}
									placeholder={t("Enter your email")}
									field={field}
									disabled
								/>
							)}
						/>

						<FormField
							control={form.control}
							name="password"
							render={({ field }) => (
								<FormPasswordInput
									passwordStrength={form.watch("password")}
									label={t("New Password")}
									placeholder={t("Enter your new password")}
									field={field}
								/>
							)}
						/>

						<FormField
							control={form.control}
							name="password_confirmation"
							render={({ field }) => (
								<FormPasswordInput
									label={t("Confirm Password")}
									placeholder={t("Confirm your new password")}
									field={field}
								/>
							)}
						/>

						<Button
							type="submit"
							className="w-full"
							disabled={form.formState.isSubmitting}
						>
							<div className="flex items-center justify-center space-x-2">
								<span>{t("Update password")}</span>
								<ArrowRight className="h-4 w-4" />
							</div>
						</Button>
					</Form>

					<div className="text-center mt-4">
						<a
							href="#!"
							onClick={modalStore.open("login")}
							className="text-sm text-primary hover:underline"
						>
							{t("Back to login")}
						</a>
					</div>
				</CardContent>
			</Card>
		</div>
	);
}
