import { useTranslation } from "react-i18next";

export function PasswordStrength({ value }) {
	const { t } = useTranslation();

	const getPasswordStrength = (password) => {
		if (!password) return { strength: 0, label: "" };

		let strength = 0;
		if (password.length >= 8) strength++;
		if (/[a-z]/.test(password)) strength++;
		if (/[A-Z]/.test(password)) strength++;
		if (/\d/.test(password)) strength++;
		if (/[^a-zA-Z\d]/.test(password)) strength++;

		const labels = [t("Very Weak"), t("Weak"), t("Fair"), t("Good"), t("Strong")];
		const colors = [
			"bg-red-500",
			"bg-orange-500",
			"bg-yellow-500",
			"bg-blue-500",
			"bg-green-500",
		];

		return {
			strength,
			label: labels[strength - 1] || "",
			color: colors[strength - 1] || "bg-gray-300",
			percentage: (strength / 5) * 100,
		};
	};

	const passwordStrength = getPasswordStrength(value);

	if (!value) return null;

	return (
		<div className="mt-3">
			<div className="flex justify-between text-xs text-muted-foreground">
				<span>{t("Password Strength")}</span>
				<span
					className={`font-medium ${passwordStrength.strength >= 3 ? "text-green-600" : "text-orange-600"}`}
				>
					{passwordStrength.label}
				</span>
			</div>
			<div className="w-full bg-gray-200 rounded-full h-1.5 mt-1">
				<div
					className={`h-1.5 rounded-full transition-all duration-300 ${passwordStrength.color}`}
					style={{ width: `${passwordStrength.percentage}%` }}
				/>
			</div>
		</div>
	);
}
