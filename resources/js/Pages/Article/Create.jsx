import { useState } from "react";
import { ArrowLeft, Save, Upload, X, Plus } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Input } from "@/Components/Ui/Form/Input";
import { Label } from "@/Components/Ui/Form/Label";
import { Textarea } from "@/Components/Ui/Form/Textarea";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Switch } from "@/Components/Ui/Form/Switch";
import { useTranslation } from "react-i18next";
import { MultiSelect } from "@/Components/Ui/Form/MultiSelect";
import { MarkdownEditor } from "@/Components/Common/MarkdownEditor";
import { router } from "@inertiajs/react";
import ApiService from "@/Services/ApiService";

export default function CreateArticlePage({ topTags, topCategories }) {
	const [isLoading, setIsLoading] = useState(false);
	const { t } = useTranslation();
	const [formData, setFormData] = useState({
		title: "",
		excerpt: "",
		content: "",
		categories: [],
		tags: [],
		published: false,
		image: "",
		sources: [],
	});
	const [image, setImage] = useState(null);
	const [errors, setErrors] = useState({});
	const [newSource, setNewSource] = useState({});

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
			formData.categories.forEach((cat, index) => {
				data[`categories[${index}]`] = cat;
			});
		}
		if (formData.tags && formData.tags.length > 0) {
			formData.tags.forEach((tag, index) => {
				data[`tags[${index}]`] = tag;
			});
		}
		if (formData.sources && formData.sources.length > 0) {
			formData.sources.forEach((source, index) => {
				data[`sources[${index}][url]`] = source.url;
				data[`sources[${index}][date]`] = source.date;
			});
		}

		if (image) {
			data.image = image;
		}

		ApiService.fetchJson(route("api.article.crud.create"), data, {
			method: "POST",
			isMultipart: true,
		})
			.then((response) => {
				if (formData.published) {
					router.visit(route("article.view", { slug: response.slug }), {
						method: "get",
					});
				} else {
					router.visit(route("article.crud.edit.view", { article: response.slug }), {
						method: "get",
					});
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
								{formData.sources.map((source, index) => (
									<div
										key={index}
										className="flex items-center justify-between p-4 border rounded-lg"
									>
										<div className="flex-1">
											<div className="flex items-center space-x-4 mb-2">
												<span className="font-medium border-r-2 border-foreground pr-4">
													{source.url}
												</span>
												<span className="font-medium">{source.date}</span>
											</div>
										</div>
										<Button
											variant="ghost"
											size="sm"
											onClick={() =>
												setFormData({
													...formData,
													sources: formData.sources.filter(
														(_, i) => i !== index,
													),
												})
											}
											className="text-red-500 hover:text-red-700"
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
											value={newSource.url}
											onChange={(e) =>
												setNewSource({
													url: e.target.value,
													date: new Date().toISOString().split("T")[0],
												})
											}
											className="w-full"
											type="url"
										/>
										{errors.sources && (
											<p className="text-sm text-red-500">{errors.sources}</p>
										)}
									</div>

									<Button
										disabled={!newSource.url}
										onClick={() => {
											setFormData({
												...formData,
												sources: [...formData.sources, newSource],
											});
											setNewSource({});
										}}
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
							{errors.image && <p className="text-sm text-red-500">{errors.image}</p>}
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
								onValueChange={(value) => handleInputChange("categories", value)}
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
								hideSelectAll
								placeholder={t("Select Options")}
							/>
						</CardContent>
						{errors.tags && <p className="text-sm text-red-500">{errors.tags}</p>}
					</Card>
				</div>
			</div>
		</div>
	);
}
