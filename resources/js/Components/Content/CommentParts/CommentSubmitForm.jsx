import ReactApi from "@/Api/ReactApi";
import { Card, CardContent } from "@/Components/Ui/Card";
import { Button } from "@/Components/Ui/Form/Button";
import { Form, FormField } from "@/Components/Ui/Form/Form";
import { FormInput } from "@/Components/Ui/Form/FormInput";
import { FormTextareaInput } from "@/Components/Ui/Form/FormTextareaInput";
import { commentSubmitSchema } from "@/Validation/ReactValidation";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";

export function CommentSubmitForm({ type, slug, parent = null, onSuccess }) {
	const { t } = useTranslation();

	const commentSubmitForm = useForm({
		resolver: zodResolver(commentSubmitSchema(t)),
		defaultValues: {
			content: "",
			parent,
		},
	});

	const commentSubmitMutation = useMutation({
		mutationFn: (data) => ReactApi.submitComment({ type, slug }, data),
		onSuccess: (data) => {
			commentSubmitForm.reset({
				content: "",
				parent,
			});
			onSuccess(data);
		},
		onError: (error) => {
			if (error?.errors) {
				Object.keys(error.errors).forEach((key) => {
					commentSubmitForm.setError(key, { message: error.errors[key][0] });
				});
			} else {
				commentSubmitForm.setError("email", {
					message: t("Invalid credentials. Please try again."),
				});
			}
			commentSubmitForm.setError("email", {
				message: t("These credentials do not match our records."),
			});
		},
	});

	const onCommentSubmit = (data) => {
		commentSubmitMutation.mutate(data);
	};

	return (
		<Form form={commentSubmitForm} onSubmit={onCommentSubmit}>
			<Card className="mb-6">
				<CardContent className="p-4">
					{parent && (
						<FormField
							control={commentSubmitForm.control}
							name="parent"
							render={({ field }) => (
								<FormInput field={field} disabled type="hidden" />
							)}
						/>
					)}

					<FormField
						control={commentSubmitForm.control}
						name="content"
						render={({ field }) => (
							<FormTextareaInput
								placeholder={t("Share your thoughts...")}
								field={field}
								className="mb-4"
							/>
						)}
					/>

					<div className="flex justify-end">
						<Button type="submit" disabled={commentSubmitMutation.isPending}>
							{t("Post Comment")}
						</Button>
					</div>
				</CardContent>
			</Card>
		</Form>
	);
}
