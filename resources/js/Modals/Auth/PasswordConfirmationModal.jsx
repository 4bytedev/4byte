import { useState } from "react";
import { Eye, EyeOff, Lock, ArrowRight } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Input } from "@/Components/Ui/Input";
import { Label } from "@/Components/Ui/Label";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { useTranslation } from "react-i18next";
import Validation from "@/Data/Validation";

export default function PasswordConfirmationModal({ onSubmit, onSuccess, open, setOpen }) {
	const [password, setPassword] = useState("");
	const [showPassword, setShowPassword] = useState(false);
	const [isLoading, setIsLoading] = useState(false);
	const [errors, setErrors] = useState({});
	const { t } = useTranslation();

	const handleInputChange = (value) => {
		setPassword(value);
		if (errors.password) setErrors({});
	};

	const validateForm = () => {
		const passwordRegex = Validation.password;
		const newErrors = {};

		if (!password) newErrors.password = t("Password is required");
		else if (password.length < 8)
			newErrors.password = t("Password must be at least 8 characters");
		else if (!passwordRegex.test(password))
			newErrors.password = t("These credentials do not match our records.");

		setErrors(newErrors);
		return Object.keys(newErrors).length === 0;
	};

	const handleSubmit = async (e) => {
		e.preventDefault();
		if (!validateForm()) return;
		setIsLoading(true);
		setErrors({});

		onSubmit(password)
			.then(() => {
				setIsLoading(false);
				setOpen(false);
				onSuccess();
			})
			.catch((response) => {
				setErrors(
					response.errors || { password: t("Incorrect password. Please try again.") },
				);
				setIsLoading(false);
			});
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

				<form onSubmit={handleSubmit} className="space-y-4">
					<div className="space-y-2">
						<Label htmlFor="password">{t("Password")}</Label>
						<div className="relative">
							<Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
							<Input
								id="password"
								type={showPassword ? "text" : "password"}
								placeholder={t("Enter your password")}
								value={password}
								onChange={(e) => handleInputChange(e.target.value)}
								className={`pl-10 pr-10 ${errors.password ? "border-red-500" : ""}`}
							/>
							<Button
								type="button"
								variant="ghost"
								size="icon"
								className="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8"
								onClick={() => setShowPassword(!showPassword)}
							>
								{showPassword ? (
									<EyeOff className="h-4 w-4" />
								) : (
									<Eye className="h-4 w-4" />
								)}
							</Button>
						</div>
						{errors.password && (
							<p className="text-sm text-red-500">{errors.password}</p>
						)}
					</div>

					<Button type="submit" className="w-full" disabled={isLoading}>
						{isLoading ? (
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
				</form>
			</ModalContent>
		</Modal>
	);
}
