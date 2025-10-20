import { useState } from "react";
import { ArrowLeft, Save, Upload } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Input } from "@/Components/Ui/Input";
import { Label } from "@/Components/Ui/Label";
import { Textarea } from "@/Components/Ui/Textarea";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Switch } from "@/Components/Ui/Switch";
import { useTranslation } from "react-i18next";
import { MultiSelect } from "@/Components/Ui/MultiSelect";
import { MarkdownEditor } from "@/Components/Ui/MarkdownEditor";
import { router } from "@inertiajs/react";
import ApiService from "@/Services/ApiService";

export default function EditArticlePage({ topTags, topCategories, article, slug }) {
	const [isLoading, setIsLoading] = useState(false);
	const { t } = useTranslation();
	const [formData, setFormData] = useState({
		title: article.title,
		excerpt: article.excerpt || "",
		content: article.content || "",
		categories: article.categories || [],
		tags: article.tags || [],
		published: article.published,
		image: article.image,
	});

	const [image, setImage] = useState(null);
	const [errors, setErrors] = useState({});

	const handleInputChange = (field, value) => {
		setFormData((prev) => ({ ...prev, [field]: value }));
	};

	const validateForm = (isDraft = false) => {
		const newErrors = {};

		if (!formData.title.trim()) {
			newErrors.title = t("Title is required");
		} else if (formData.title.trim().length < 10) {
			newErrors.title = t("Title must be at least 10 characters");
		}

		if (!isDraft) {
			if (!formData.excerpt.trim()) {
				newErrors.excerpt = t("Excerpt is required");
			} else if (formData.excerpt.trim().length < 100) {
				newErrors.excerpt = t("Excerpt must be at least 100 characters");
			}

			if (!formData.content.trim()) {
				newErrors.content = t("Content is required");
			} else if (formData.content.trim().length < 500) {
				newErrors.content = t("Content must be at least 500 characters");
			}

			if (!formData.categories || formData.categories.length === 0) {
				newErrors.categories = t("Select at least 1 category");
			} else if (formData.categories.length > 3) {
				newErrors.categories = t("You can select up to 3 categories");
			}

			if (!formData.tags || formData.tags.length === 0) {
				newErrors.tags = t("Select at least 1 tag");
			} else if (formData.tags.length > 3) {
				newErrors.tags = t("You can select up to 3 tags");
			}

			if (!image) {
				newErrors.image = t("Cover image is required");
			}
		}

		setErrors(newErrors);
		return Object.keys(newErrors).length === 0;
	};

	const handleSubmit = async () => {
		if (!validateForm(!formData.published)) return;
		setIsLoading(true);

		const data = {
			title: formData.title,
			excerpt: formData.excerpt,
			content: formData.content,
			published: formData.published,
		};
		if (formData.categories && formData.categories.length > 0) {
			formData.categories.forEach((social, index) => {
				data[`categories[${index}]`] = social;
			});
		}
		if (formData.tags && formData.tags.length > 0) {
			formData.tags.forEach((social, index) => {
				data[`tags[${index}]`] = social;
			});
		}

		if (image) {
			data.image = image;
		}

		ApiService.fetchJson(route("api.article.crud.edit", { article: slug }), data, {
			method: "POST",
			isMultipart: true,
		})
			.then((response) => {
				if (formData.published) {
					router.visit(route("article.view", { slug: response.slug }), {
						method: "get",
					});
				} else {
					if (slug != response.slug) {
						router.visit(route("article.crud.edit.view", { article: response.slug }), {
							method: "get",
						});
					}
				}
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
			})
			.finally(() => {
				setIsLoading(false);
			});
	};

	return (
		<div className="container mx-auto px-4 py-8">
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
						<Button onClick={() => handleSubmit(false)} disabled={isLoading}>
							<Save className="h-4 w-4 mr-2" />
							{isLoading ? t("Publishing...") : t("Publish")}
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
								<Label htmlFor="title">{t("Title")}</Label>
								<Input
									id="title"
									placeholder={t("Enter article title...")}
									value={formData.title}
									onChange={(e) => handleInputChange("title", e.target.value)}
								/>
								{errors.title && (
									<p className="text-sm text-red-500">{errors.title}</p>
								)}
							</div>

							<div className="space-y-2">
								<Label htmlFor="excerpt">
									Excerpt {!formData.published || "*"}
								</Label>
								<Textarea
									id="excerpt"
									placeholder={t("Brief description of your article...")}
									value={formData.excerpt}
									onChange={(e) => handleInputChange("excerpt", e.target.value)}
									rows={3}
								/>
								{errors.excerpt && (
									<p className="text-sm text-red-500">{errors.excerpt}</p>
								)}
							</div>
						</CardContent>
					</Card>

					{/* Content Editor */}
					<MarkdownEditor
						textareaProps={{
							placeholder: t(
								"Write your article content here... (Markdown supported)",
							),
						}}
						value={formData.content}
						onChange={(value) => handleInputChange("content", value)}
					/>
					{errors.content && <p className="text-sm text-red-500">{errors.content}</p>}

					{/* Sidebar */}
					<div className="space-y-6">
						{/* Publishing Options */}
						<Card>
							<CardHeader>
								<CardTitle>{t("Publishing")}</CardTitle>
							</CardHeader>
							<CardContent className="space-y-4">
								<div className="flex items-center justify-between">
									<div>
										<Label>{t("Publish immediately")}</Label>
										<p className="text-sm text-muted-foreground">
											{t("Make article visible to everyone")}
										</p>
									</div>
									<Switch
										checked={formData.published}
										onCheckedChange={(checked) =>
											handleInputChange("published", checked)
										}
									/>
								</div>
							</CardContent>
						</Card>

						{/* Cover Image */}
						<Card>
							<CardHeader>
								<CardTitle>
									{t("Cover Image")} {!formData.published || "*"}
								</CardTitle>
							</CardHeader>
							<CardContent>
								<div
									className="border-2 border-dashed border-muted-foreground/25 rounded-lg p-8 text-center bg-no-repeat bg-cover bg-center "
									style={{ backgroundImage: `url(${formData.image})` }}
								>
									{!formData.image ? (
										<>
											<Upload className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
											<p className="text-muted-foreground mb-4">
												{t("Drag and drop an image, or click to browse")}
											</p>
										</>
									) : (
										<div className="h-32"></div>
									)}
									<Label htmlFor="cover-input" className="cursor-pointer">
										<Button variant="outline" asChild>
											<Upload className="h-4 w-4 mr-2" />
											{t("Change Cover")}
										</Button>
									</Label>
									<input
										hidden
										type="file"
										id="cover-input"
										accept="image/*"
										onChange={(e) => {
											const file = e.target.files[0];
											if (file) {
												setImage(file);

												const previewUrl = URL.createObjectURL(file);

												handleInputChange("image", previewUrl);
											}
										}}
									/>
								</div>
								{errors.image && (
									<p className="text-sm text-red-500">{errors.image}</p>
								)}
							</CardContent>
						</Card>

						{/* Category */}
						<Card>
							<CardHeader>
								<CardTitle>
									{t("Categories")} {!formData.published || "*"}
								</CardTitle>
							</CardHeader>
							<CardContent>
								<MultiSelect
									options={topCategories.map((c) => ({
										label: `${c.name} (${c.total})`,
										value: c.slug,
									}))}
									onValueChange={(value) =>
										handleInputChange("categories", value)
									}
									defaultValue={formData.categories}
									hideSelectAll
									placeholder={t("Select Options")}
								/>
							</CardContent>
							{errors.categories && (
								<p className="text-sm text-red-500">{errors.categories}</p>
							)}
						</Card>

						{/* Tags */}
						<Card>
							<CardHeader>
								<CardTitle>
									{t("Tags")} {!formData.published || "*"}
								</CardTitle>
							</CardHeader>
							<CardContent>
								<MultiSelect
									options={topTags.map((c) => ({
										label: `${c.name} (${c.total})`,
										value: c.slug,
									}))}
									onValueChange={(value) => handleInputChange("tags", value)}
									defaultValue={formData.tags}
									hideSelectAll
									placeholder={t("Select Options")}
								/>
							</CardContent>
							{errors.tags && <p className="text-sm text-red-500">{errors.tags}</p>}
						</Card>
					</div>
				</div>
			</div>
		</div>
	);
}
