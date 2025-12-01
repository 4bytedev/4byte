import { useState } from "react";
import { ArrowLeft, Save, Upload, X, Plus } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Input } from "@/Components/Ui/Form/Input";
import { Label } from "@/Components/Ui/Form/Label";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Switch } from "@/Components/Ui/Form/Switch";
import { useTranslation } from "react-i18next";
import { MultiSelect } from "@/Components/Ui/Form/MultiSelect";
import { router } from "@inertiajs/react";
import { useFieldArray, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { createArticleSchema } from "@/Validation/ContentValidation";
import { useMutation } from "@tanstack/react-query";
import ContentApi from "@/Api/ContentApi";
import {
	Form,
	FormControl,
	FormDescription,
	FormField,
	FormItem,
	FormLabel,
	FormMessage,
} from "@/Components/Ui/Form/Form";
import { FormInput } from "@/Components/Ui/Form/FormInput";
import { FormTextareaInput } from "@/Components/Ui/Form/FormTextareaInput";
import { FormMarkdownInput } from "@/Components/Ui/Form/FormMarkdownInput";

export default function CreateArticlePage({ topTags, topCategories }) {
	console.log(topCategories, topTags);

	const { t } = useTranslation();
	const [imagePreview, setImagePreview] = useState(null);
	const [newSourceUrl, setNewSourceUrl] = useState("");

	const form = useForm({
		resolver: zodResolver(createArticleSchema(t)),
		defaultValues: {
			title: "",
			excerpt: "",
			content: "",
			categories: [],
			tags: [],
			published: false,
			image: undefined,
			sources: [],
		},
	});

	const { fields, append, remove } = useFieldArray({
		control: form.control,
		name: "sources",
	});

	const createMutation = useMutation({
		mutationFn: (data) => {
			const payload = {
				title: data.title,
				excerpt: data.excerpt,
				content: data.content,
				published: data.published,
			};

			if (data.image) {
				payload.image = data.image;
			}

			if (data.categories && data.categories.length > 0) {
				data.categories.forEach((cat, index) => {
					payload[`categories[${index}]`] = cat;
				});
			}
			if (data.tags && data.tags.length > 0) {
				data.tags.forEach((tag, index) => {
					payload[`tags[${index}]`] = tag;
				});
			}
			if (data.sources && data.sources.length > 0) {
				data.sources.forEach((source, index) => {
					payload[`sources[${index}][url]`] = source.url;
					payload[`sources[${index}][date]`] = source.date;
				});
			}

			return ContentApi.createArticle(payload);
		},
		onSuccess: (response) => {
			if (response.published) {
				router.visit(route("article.view", { slug: response.slug }), {
					method: "get",
				});
			} else {
				router.visit(route("article.crud.edit.view", { article: response.slug }), {
					method: "get",
				});
			}
		},
		onError: (error) => {
			if (error?.errors) {
				Object.keys(error.errors).forEach((key) => {
					form.setError(key, { message: error.errors[key][0] });
				});
			} else {
				form.setError("title", { message: t("Invalid credentials. Please try again.") });
			}
		},
	});

	const onSubmit = (data) => {
		createMutation.mutate(data);
	};

	const handleAddSource = () => {
		if (!newSourceUrl) return;
		append({
			url: newSourceUrl,
			date: new Date().toISOString().split("T")[0],
		});
		setNewSourceUrl("");
	};

	return (
		<div className="container mx-auto px-4 py-8">
			<Form form={form} onSubmit={onSubmit}>
				<div className="max-w-6xl mx-auto">
					{/* Header */}
					<div className="flex items-center justify-between mb-8">
						<div className="flex items-center space-x-4">
							<Button variant="ghost" onClick={() => window.history.back()}>
								<ArrowLeft className="h-4 w-4 mr-2" />
								{t("Back")}
							</Button>
							<div>
								<h1 className="text-3xl font-bold">{t("Create New Article")}</h1>
								<p className="text-muted-foreground">
									{t("Share your knowledge with the developer community")}
								</p>
							</div>
						</div>
						<div className="flex items-center space-x-2">
							<Button type="submit" disabled={createMutation.isPending}>
								<Save className="h-4 w-4 mr-2" />
								{createMutation.isPending ? t("Publishing...") : t("Publish")}
							</Button>
						</div>
					</div>

					<div className="flex flex-col gap-8">
						{/* Basic Information */}
						<Card>
							<CardHeader>
								<CardTitle>{t("Article Details")}</CardTitle>
							</CardHeader>
							<CardContent className="space-y-4">
								<div className="space-y-2">
									<FormField
										control={form.control}
										name="title"
										render={({ field }) => (
											<FormInput
												label={t("Title") + " *"}
												placeholder={t("Enter article title...")}
												field={field}
											/>
										)}
									/>
								</div>

								<div className="space-y-2">
									<FormField
										control={form.control}
										name="excerpt"
										render={({ field }) => (
											<FormTextareaInput
												label={
													t("Excerpt") +
													(form.watch("published") ? " *" : "")
												}
												placeholder={t(
													"Brief description of your article...",
												)}
												field={field}
											/>
										)}
									/>
								</div>
							</CardContent>
						</Card>

						{/* Content Editor */}
						<FormField
							control={form.control}
							name="content"
							render={({ field }) => (
								<FormMarkdownInput
									placeholder={t(
										"Write your article content here... (Markdown supported)",
									)}
									field={field}
								/>
							)}
						/>
						<Card>
							<CardHeader>
								<CardTitle>{t("Sources")}</CardTitle>
								<p className="text-sm text-muted-foreground">
									{t("Cite your sources and share your knowledge")}
								</p>
							</CardHeader>
							<CardContent className="space-y-6">
								{/* Current Sources */}
								<div className="space-y-4">
									{fields.map((field, index) => (
										<div
											key={index}
											className="flex items-center justify-between p-4 border rounded-lg"
										>
											<div className="flex-1">
												<div className="flex items-center space-x-4 mb-2">
													<span className="font-medium border-r-2 border-foreground pr-4">
														{field.url}
													</span>
													<span className="font-medium">
														{field.date}
													</span>
												</div>
											</div>
											<Button
												variant="ghost"
												size="sm"
												onClick={() => remove(index)}
												className="text-red-500 hover:text-red-700"
												type="button"
											>
												<X className="h-4 w-4" />
											</Button>
										</div>
									))}
								</div>

								{/* Add New Source */}
								<div className="border-t pt-4">
									<div className="flex gap-4 items-end">
										<div className="space-y-2 w-full">
											<Label>{t("Source Url")}</Label>
											<Input
												id="account-url"
												value={newSourceUrl}
												onChange={(e) => setNewSourceUrl(e.target.value)}
												onKeyDown={(e) => {
													if (e.key === "Enter") {
														e.preventDefault();
														handleAddSource();
													}
												}}
												className="w-full"
												type="url"
											/>
										</div>

										<Button
											disabled={!newSourceUrl}
											type="button"
											onClick={handleAddSource}
										>
											<Plus className="h-4 w-4 mr-2" />
											{t("Add Source")}
										</Button>
									</div>
								</div>
							</CardContent>
						</Card>

						{/* Publishing Options */}
						<Card>
							<CardHeader>
								<CardTitle>{t("Publishing")}</CardTitle>
							</CardHeader>
							<CardContent className="space-y-4">
								<FormField
									control={form.control}
									name="published"
									render={({ field }) => (
										<FormItem className="flex items-center justify-between space-y-0">
											<div className="space-y-0.5">
												<FormLabel>{t("Publish immediately")}</FormLabel>
												<FormDescription>
													{t("Make article visible to everyone")}
												</FormDescription>
											</div>
											<FormControl>
												<Switch
													checked={field.value}
													onCheckedChange={field.onChange}
												/>
											</FormControl>
										</FormItem>
									)}
								/>
							</CardContent>
						</Card>

						{/* Cover Image */}
						<Card>
							<CardHeader>
								<CardTitle>
									{t("Cover Image")} {form.watch("published") ? "*" : ""}
								</CardTitle>
							</CardHeader>
							<CardContent>
								<FormField
									control={form.control}
									name="image"
									// eslint-disable-next-line no-unused-vars
									render={({ field: { value, onChange, ...fieldProps } }) => (
										<FormItem>
											<FormControl>
												<div
													className="border-2 border-dashed border-muted-foreground/25 rounded-lg p-8 text-center bg-no-repeat bg-cover bg-center"
													style={{
														backgroundImage: `url(${imagePreview})`,
													}}
												>
													{!imagePreview ? (
														<>
															<Upload className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
															<p className="text-muted-foreground mb-4">
																{t(
																	"Drag and drop an image, or click to browse",
																)}
															</p>
														</>
													) : (
														<div className="h-32"></div>
													)}
													<FormLabel
														htmlFor="cover-input"
														className="cursor-pointer"
													>
														<div className="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
															<Upload className="h-4 w-4 mr-2" />
															{t("Change Cover")}
														</div>
													</FormLabel>
													<Input
														{...fieldProps}
														id="cover-input"
														type="file"
														accept="image/*"
														className="hidden"
														onChange={(event) => {
															const file =
																event.target.files &&
																event.target.files[0];
															if (file) {
																onChange(file);
																setImagePreview(
																	URL.createObjectURL(file),
																);
															}
														}}
													/>
												</div>
											</FormControl>
											<FormMessage />
										</FormItem>
									)}
								/>
							</CardContent>
						</Card>

						{/* Category */}
						<Card>
							<CardHeader>
								<CardTitle>
									{t("Categories")} {form.watch("published") ? "*" : ""}
								</CardTitle>
							</CardHeader>
							<CardContent>
								<FormField
									control={form.control}
									name="categories"
									render={({ field }) => (
										<FormItem>
											<FormControl>
												<MultiSelect
													options={topCategories.map((c) => ({
														label: `${c.data.name} (${c.total})`,
														value: c.data.slug,
													}))}
													onValueChange={field.onChange}
													defaultValue={field.value}
													hideSelectAll
													placeholder={t("Select Options")}
												/>
											</FormControl>
											<FormMessage />
										</FormItem>
									)}
								/>
							</CardContent>
						</Card>

						{/* Tags */}
						<Card>
							<CardHeader>
								<CardTitle>
									{t("Tags")} {form.watch("published") ? "*" : ""}
								</CardTitle>
							</CardHeader>
							<CardContent>
								<FormField
									control={form.control}
									name="tags"
									render={({ field }) => (
										<FormItem>
											<FormControl>
												<MultiSelect
													options={topTags.map((c) => ({
														label: `${c.data.name} (${c.total})`,
														value: c.data.slug,
													}))}
													onValueChange={field.onChange}
													defaultValue={field.value}
													hideSelectAll
													placeholder={t("Select Options")}
												/>
											</FormControl>
											<FormMessage />
										</FormItem>
									)}
								/>
							</CardContent>
						</Card>
					</div>
				</div>
			</Form>
		</div>
	);
}
