import { useRef, useState } from "react";
import { Card, CardContent, CardFooter } from "@/Components/Ui/Card";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Separator } from "@/Components/Ui/Separator";
import {
	DropdownMenu,
	DropdownMenuContent,
	DropdownMenuItem,
	DropdownMenuTrigger,
} from "@/Components/Ui/Form/DropdownMenu";
import { ArrowRight, Image, Pencil } from "lucide-react";
import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { useAuthStore } from "@/Stores/AuthStore";
import { Button } from "@/Components/Ui/Form/Button";
import { ImageSlider } from "@/Components/Common/ImageSlider";
import { toast } from "@/Hooks/useToast";
import { createEntrySchema } from "@/Validation/ContentValidation";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { useMutation } from "@tanstack/react-query";
import { Form, FormField, FormMessage } from "../Ui/Form/Form";
import { FormTextareaInput } from "../Ui/Form/FormTextareaInput";
import { Label } from "../Ui/Form/Label";
import ContentApi from "@/Api/ContentApi";

export function CreatorCard() {
	const { t } = useTranslation();
	const authStore = useAuthStore();
	const [expanded, setExpanded] = useState(false);
	const fileInputRef = useRef(null);

	const form = useForm({
		resolver: zodResolver(createEntrySchema(t)),
		defaultValues: {
			content: "",
			media: [],
		},
	});

	const watchedMedia = form.watch("media");
	const watchedContent = form.watch("content");

	const createEntryMutation = useMutation({
		mutationFn: (data) => {
			const payload = { content: data.content };

			if (data.media.length > 0) {
				data.media.forEach((mediaItem, index) => {
					payload[`media[${index}]`] = mediaItem.file;
				});
			}

			return ContentApi.createEntry(payload);
		},
		onSuccess: () => {
			toast({
				title: t("Successfully!"),
				description: t("Your entry is live! It might take a moment to show up."),
			});
			form.reset();
			setExpanded(false);
		},
		onError: (error) => {
			if (error?.errors) {
				Object.keys(error.errors).forEach((key) => {
					form.setError(key, { message: error.errors[key][0] });
				});
			} else {
				toast({
					variant: "destructive",
					title: t("Error"),
					description: t("Something went wrong. Please try again."),
				});
			}
		},
	});

	const onSubmit = (data) => {
		createEntryMutation.mutate(data);
	};

	const handleFileChange = (e) => {
		const files = Array.from(e.target.files || []);
		if (files.length === 0) return;

		const currentMedias = form.getValues("media");
		const existingIds = new Set(currentMedias.map((item) => item.id));

		const newFiles = files
			.map((file) => ({
				image: URL.createObjectURL(file),
				file,
				id: `${file.name}_${file.size}_${file.lastModified}`,
			}))
			.filter((fileObj) => !existingIds.has(fileObj.id));

		form.setValue("media", [...currentMedias, ...newFiles], { shouldValidate: true });

		if (fileInputRef.current) fileInputRef.current.value = "";
	};

	const removeMedia = (id) => {
		const currentMedias = form.getValues("media");
		const filtered = currentMedias.filter((m) => m.id !== id);
		form.setValue("media", filtered, { shouldValidate: true });
	};

	return (
		<>
			{expanded && (
				<div
					className="fixed inset-0 bg-black/50 z-modal"
					onClick={() => setExpanded(false)}
				/>
			)}

			<Card
				className={`mb-4 z-popover ${
					expanded
						? "fixed top-[250px] left-1/2 w-full max-w-2xl -translate-x-1/2 -translate-y-1/2 shadow-2xl"
						: "relative"
				}`}
			>
				<Form className="space-y-0" form={form} onSubmit={onSubmit}>
					<CardContent className="p-2">
						<div className="flex items-center gap-3 mb-2">
							<Avatar className="h-10 w-10">
								<AvatarImage
									src={authStore.user.avatar || "/placeholder-avatar.jpg"}
								/>
								<AvatarFallback>
									{authStore.user.name?.substring(0, 1) || "U"}
								</AvatarFallback>
							</Avatar>
							<div className="w-full">
								<FormField
									control={form.control}
									name="content"
									render={({ field }) => (
										<FormTextareaInput
											field={field}
											placeholder={t("What are you thinking?")}
											onFocus={() => setExpanded(true)}
											rows={expanded ? 5 : 3}
										/>
									)}
								/>

								<FormField name="media" render={() => <FormMessage />} />
							</div>
						</div>

						{watchedMedia.length > 0 && expanded && (
							<div className="mt-2">
								<ImageSlider
									medias={watchedMedia}
									onRemove={removeMedia}
									spaceBetween={8}
									slidesPerView={4}
									className="h-20"
								/>
							</div>
						)}
					</CardContent>

					<CardContent className="py-0">
						<Separator />
					</CardContent>

					<CardFooter className="flex py-3 justify-between">
						<div className="flex gap-2">
							<DropdownMenu>
								<DropdownMenuTrigger asChild>
									<Button type="button" variant="ghost" className="px-2 py-0">
										<Pencil className="w-4 h-4 mr-2" />
										{t("Write")}
									</Button>
								</DropdownMenuTrigger>
								<DropdownMenuContent align="start" className="w-48">
									<DropdownMenuItem asChild>
										<Link
											href={route("article.crud.create.view")}
											className="flex w-full h-full"
										>
											{t("Article")}
										</Link>
									</DropdownMenuItem>
								</DropdownMenuContent>
							</DropdownMenu>

							<Button variant="ghost" type="button" disabled={!expanded}>
								<Label
									type="button"
									variant="ghost"
									className="px-2 py-0 flex items-center cursor-pointer w-full h-full"
									onClick={() => expanded && fileInputRef.current?.click()}
								>
									<Image className="w-4 h-4 mr-2" />
									{t("Media")}
								</Label>

								<input
									type="file"
									accept="image/*"
									multiple
									className="hidden"
									ref={fileInputRef}
									onChange={handleFileChange}
								/>
							</Button>
						</div>

						<Button
							type="submit"
							size="sm"
							disabled={
								createEntryMutation.isPending ||
								(!expanded && watchedContent.length === 0)
							}
							className={createEntryMutation.isPending ? "opacity-80" : ""}
						>
							{createEntryMutation.isPending ? (
								<>
									<div className="animate-spin rounded-full h-3 w-3 border-b-2 border-white mr-2"></div>
									{t("Publishing...")}
								</>
							) : (
								<>
									<ArrowRight className="w-4 h-4 mr-2" />
									{t("Publish")}
								</>
							)}
						</Button>
					</CardFooter>
				</Form>
			</Card>
		</>
	);
}
