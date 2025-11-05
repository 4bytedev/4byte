import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { ArrowRight } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { Form, FormField } from "@/Components/Ui/Form/Form";
import { useTranslation } from "react-i18next";
import { useMutation } from "@tanstack/react-query";
import { passwordConfirmationSchema } from "@/Validation/UserValidation";
import { FormPasswordInput } from "@/Components/Ui/Form/FormPasswordInput";

export default function PasswordConfirmationModal({ onSubmit, onSuccess, open, setOpen }) {
	const { t } = useTranslation();

	const form = useForm({
		resolver: zodResolver(passwordConfirmationSchema(t)),
		defaultValues: { password: "" },
	});

	const loginMutation = useMutation({
		mutationFn: (data) => onSubmit(data),
		onSuccess: () => {
			setOpen(false);
			onSuccess();
			form.reset();
		},
		onError: () => {
			form.setError("password", {
				message: t("Incorrect password. Please try again."),
			});
		},
	});

	const handleSubmit = (data) => {
		loginMutation.mutate(data);
	};

	return (
		<Modal open={open} onOpenChange={() => setOpen(!open)}>
			<ModalContent>
				<ModalTitle className="text-center mb-2 text-2xl">
					{t("Confirm Password")}
				</ModalTitle>
				<ModalDescription className="text-center text-gray-500 mb-6">
					{t("Please enter your password to continue")}
				</ModalDescription>

				<Form form={form} onSubmit={handleSubmit} className="space-y-4">
					<FormField
						control={form.control}
						name="password"
						render={({ field }) => (
							<FormPasswordInput label={t("Password")} field={field} />
						)}
					/>

					<Button type="submit" className="w-full" disabled={form.formState.isSubmitting}>
						{form.formState.isSubmitting ? (
							<div className="flex items-center space-x-2 justify-center">
								<div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
								<span>{t("Loading...")}</span>
							</div>
						) : (
							<div className="flex items-center space-x-2 justify-center">
								<span>{t("Confirm")}</span>
								<ArrowRight className="h-4 w-4" />
							</div>
						)}
					</Button>
				</Form>
			</ModalContent>
		</Modal>
	);
}
