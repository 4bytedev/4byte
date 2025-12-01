import * as z from "zod";

export const createEntrySchema = (t) =>
	z
		.object({
			content: z.string().default(""),
			media: z.array(z.any()).default([]),
		})
		.superRefine((data, ctx) => {
			const hasMedia = data.media.length > 0;
			const contentLen = data.content.trim().length;

			if (!hasMedia) {
				if (contentLen < 50) {
					ctx.addIssue({
						code: z.ZodIssueCode.custom,
						message: t("Content must be at least 50 characters"),
						path: ["content"],
					});
				}
				if (contentLen > 350) {
					ctx.addIssue({
						code: z.ZodIssueCode.custom,
						message: t("Content must be at most 350 characters"),
						path: ["content"],
					});
				}
			}

			if (data.media.length > 10) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("You can upload up to 10 media files"),
					path: ["media"],
				});
			}

			if (contentLen === 0 && !hasMedia) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("You must provide either content or at least one media"),
					path: ["content"],
				});
			}
		});

export const createArticleSchema = (t) =>
	z
		.object({
			title: z
				.string()
				.trim()
				.min(1, t("Title is required"))
				.min(10, t("Title must be at least 10 characters")),
			excerpt: z.string().trim().default(""),
			content: z.string().trim().default(""),
			categories: z.array(z.string()).default([]),
			tags: z.array(z.string()).default([]),
			image: z.any().nullable().optional(),
			sources: z.array(
				z.object({
					url: z.url(),
					date: z.string().refine((val) => !isNaN(Date.parse(val))),
				}),
			),
			published: z.boolean().default(false),
		})
		.superRefine((data, ctx) => {
			if (!data.published) return;
			if (!data.excerpt) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("Excerpt is required"),
					path: ["excerpt"],
				});
			} else if (data.excerpt.length < 100) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("Excerpt must be at least 100 characters"),
					path: ["excerpt"],
				});
			}

			if (!data.content) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("Content is required"),
					path: ["content"],
				});
			} else if (data.content.length < 500) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("Content must be at least 500 characters"),
					path: ["content"],
				});
			}

			if (!data.categories || data.categories.length === 0) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("Select at least 1 category"),
					path: ["categories"],
				});
			} else if (data.categories.length > 3) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("You can select up to 3 categories"),
					path: ["categories"],
				});
			}

			if (!data.tags || data.tags.length === 0) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("Select at least 1 tag"),
					path: ["tags"],
				});
			} else if (data.tags.length > 3) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("You can select up to 3 tags"),
					path: ["tags"],
				});
			}

			if (!data.image) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("Cover image is required"),
					path: ["image"],
				});
			}
		});
