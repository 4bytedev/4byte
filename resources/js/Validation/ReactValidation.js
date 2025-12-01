import * as z from "zod";

export const commentSubmitSchema = (t) =>
	z.object({
		content: z.string().min(20, t("Comment must be at least 20 characters")),
		parent: z.number().optional().nullable(),
	});
