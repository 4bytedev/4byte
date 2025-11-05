import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Mail, ArrowRight } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Checkbox } from "@/Components/Ui/Form/Checkbox";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { Form, FormField, FormItem, FormLabel, FormControl } from "@/Components/Ui/Form/Form";
import { useAuthStore } from "@/Stores/AuthStore";
import { useModalStore } from "@/Stores/ModalStore";
import { useSiteStore } from "@/Stores/SiteStore";
import { useTranslation } from "react-i18next";
import AuthApi from "@/Api/AuthApi";
import { loginSchema } from "@/Validation/AuthValidation";
import { useMutation } from "@tanstack/react-query";
import { FormInput } from "@/Components/Ui/Form/FormInput";
import { FormPasswordInput } from "@/Components/Ui/Form/FormPasswordInput";

export default function LoginModal() {
	const authStore = useAuthStore();
	const modalStore = useModalStore();
	const siteStore = useSiteStore();
	const { t } = useTranslation();

	const form = useForm({
		resolver: zodResolver(loginSchema(t)),
		defaultValues: {
			email: "",
			password: "",
			rememberMe: false,
		},
	});

	const loginMutation = useMutation({
		mutationFn: (data) => AuthApi.login(data),
		onSuccess: (response) => {
			authStore.setUser(response);
			modalStore.close("login");
		},
		onError: () => {
			form.setError("email", {
				message: t("These credentials do not match our records."),
			});
		},
	});

	const onSubmit = (data) => {
		loginMutation.mutate(data);
	};

	return (
		<Modal open={modalStore.login} onOpenChange={modalStore.toggle("login")}>
			<ModalContent>
				<ModalTitle className="text-center mb-2 text-2xl">{t("Sign In")}</ModalTitle>
				<ModalDescription className="text-center text-gray-500 mb-6">
					{t("Enter your credentials to access your account")}
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

					<FormField
						control={form.control}
						name="password"
						render={({ field }) => (
							<FormPasswordInput label={t("Password")} field={field} />
						)}
					/>

					<FormField
						control={form.control}
						name="rememberMe"
						render={({ field }) => (
							<FormItem className="flex flex-row items-center justify-between">
								<div className="flex items-center space-x-2">
									<FormControl>
										<Checkbox
											checked={field.value}
											onCheckedChange={field.onChange}
										/>
									</FormControl>
									<FormLabel className="text-sm">{t("Remember me")}</FormLabel>
								</div>
								<a
									href="#!"
									onClick={modalStore.open("forgotPassword")}
									className="text-sm text-primary hover:underline"
								>
									{t("Forgot Password?")}
								</a>
							</FormItem>
						)}
					/>

					<Button type="submit" className="w-full" disabled={loginMutation.isPending}>
						{loginMutation.isPending ? (
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
				</Form>

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
