import * as z from "zod";
import Validation from "@/Data/Validation";

export const passwordConfirmationSchema = (t) =>
	z.object({
		password: z
			.string()
			.min(8, t("Password must be at least 8 characters"))
			.regex(Validation.password, t("These credentials do not match our records.")),
	});

export const accountSettingsSchema = (t) =>
	z.object({
		avatar: z.string().optional(),
		name: z.string().min(1, t("Full name is required")),
		username: z
			.string()
			.min(1, t("Username is required"))
			.regex(Validation.username, t("Invalid username format")),
		email: z.string().email(t("Invalid email format")),
	});

export const profileSettingsSchema = (t) =>
	z.object({
		bio: z.string().optional(),
		location: z.string().optional(),
		website: z.string().optional(),
		role: z.string().optional(),
		socials: z.array(z.string().url(t("Invalid URL"))).optional(),
		cover: z.string().optional(),
	});

export const securitySettingsSchema = (t) =>
	z
		.object({
			current_password: z
				.string()
				.min(8, t("Password must be at least 8 characters"))
				.regex(Validation.password, t("These credentials do not match our records.")),
			new_password: z
				.string()
				.min(8, t("Password must be at least 8 characters"))
				.regex(Validation.password, t("These credentials do not match our records.")),
			new_password_confirmation: z.string().min(1, t("Password confirmation is required")),
		})
		.refine((data) => data.new_password === data.new_password_confirmation, {
			message: t("Passwords do not match"),
			path: ["new_password_confirmation"],
		});
