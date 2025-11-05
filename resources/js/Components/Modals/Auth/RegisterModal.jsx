import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Mail, User, Fingerprint, ArrowRight } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Checkbox } from "@/Components/Ui/Form/Checkbox";
import { Form, FormField, FormItem, FormControl, FormMessage } from "@/Components/Ui/Form/Form";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { useAuthStore } from "@/Stores/AuthStore";
import { useModalStore } from "@/Stores/ModalStore";
import { useSiteStore } from "@/Stores/SiteStore";
import { Trans, useTranslation } from "react-i18next";
import { useMutation } from "@tanstack/react-query";
import AuthApi from "@/Api/AuthApi";
import { registerSchema } from "@/Validation/AuthValidation";
import { FormInput } from "@/Components/Ui/Form/FormInput";
import { Link } from "@inertiajs/react";
import { FormPasswordInput } from "@/Components/Ui/Form/FormPasswordInput";

export default function RegisterModal() {
	const authStore = useAuthStore();
	const modalStore = useModalStore();
	const siteStore = useSiteStore();
	const { t } = useTranslation();

	const form = useForm({
		resolver: zodResolver(registerSchema(t)),
		defaultValues: {
			name: "",
			email: "",
			username: "",
			password: "",
			password_confirmation: "",
			termsAccepted: false,
		},
	});

	const registerMutation = useMutation({
		mutationFn: (data) => AuthApi.register(data),
		onSuccess: (response) => {
			authStore.setUser(response);
			modalStore.close("register");
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
		registerMutation.mutate(data);
	};

	return (
		<Modal open={modalStore.register} onOpenChange={modalStore.toggle("register")}>
			<ModalContent>
				<ModalTitle className="text-center mb-2 text-2xl">
					{t("Create a New Account")}
				</ModalTitle>
				<ModalDescription className="text-center mb-6">
					{t("Join our community by filling out your information below")}
				</ModalDescription>

				<Form form={form} onSubmit={onSubmit} className="space-y-4">
					<FormField
						control={form.control}
						name="name"
						render={({ field }) => (
							<FormInput
								icon={User}
								label={t("Full Name")}
								placeholder={t("Enter your full name")}
								field={field}
							/>
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
							/>
						)}
					/>

					<FormField
						control={form.control}
						name="username"
						render={({ field }) => (
							<FormInput
								icon={Fingerprint}
								label={t("Username")}
								placeholder={t("Enter your username")}
								field={field}
							/>
						)}
					/>

					<FormField
						control={form.control}
						name="password"
						render={({ field }) => (
							<FormPasswordInput
								passwordStrength={form.watch("password")}
								label={t("Password")}
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
								placeholder={t("Confirm your password")}
								field={field}
							/>
						)}
					/>

					<FormField
						control={form.control}
						name="termsAccepted"
						render={({ field }) => (
							<FormItem>
								<div className="flex items-start space-x-2">
									<FormControl>
										<Checkbox
											checked={field.value}
											onCheckedChange={field.onChange}
										/>
									</FormControl>
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
									</div>
								</div>
								<FormMessage />
							</FormItem>
						)}
					/>

					<Button type="submit" className="w-full" disabled={registerMutation.isPending}>
						{registerMutation.isPending ? (
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
				</Form>

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
