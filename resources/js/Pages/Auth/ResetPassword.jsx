import { useState, useEffect } from "react";
import { Link } from "@inertiajs/react";
import { Eye, EyeOff, Lock, ArrowRight, CheckCircle, AlertCircle, Mail } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Input } from "@/Components/Ui/Input";
import { Label } from "@/Components/Ui/Label";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import ApiService from "@/Services/ApiService";
import { useModalStore } from "@/Stores/ModalStore";
import Validation from "@/Data/Validation";

export default function ResetPasswordPage({ email, token }) {
	const [formData, setFormData] = useState({
		password: "",
		password_confirmation: "",
	});
	const [showPassword, setShowPassword] = useState(false);
	const [showConfirmPassword, setShowConfirmPassword] = useState(false);
	const [isLoading, setIsLoading] = useState(false);
	const [isSuccess, setIsSuccess] = useState(false);
	const [errors, setErrors] = useState({});
	const [tokenValid, setTokenValid] = useState(null);
	const modalStore = useModalStore();

	useEffect(() => {
		const validateToken = async () => {
			if (!token) {
				setTokenValid(false);
				return;
			}

			try {
				await new Promise((resolve) => setTimeout(resolve, 1000));
				setTokenValid(true);
			} catch {
				setTokenValid(false);
			}
		};

		validateToken();
	}, [token]);

	const handleInputChange = (field, value) => {
		setFormData((prev) => ({ ...prev, [field]: value }));
		if (errors[field]) {
			setErrors((prev) => ({ ...prev, [field]: "" }));
		}
	};

	const validateForm = () => {
		const newErrors = {};
		const passwordRegex = Validation.password;

		if (!formData.password) newErrors.password = "Password is required";
		else if (formData.password.length < 8)
			newErrors.password = "Password must be at least 8 characters";
		else if (!passwordRegex.test(formData.password))
			newErrors.password =
				"Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
		if (!formData.password_confirmation)
			newErrors.password_confirmation = "Password confirmation is required";
		else if (formData.password !== formData.password_confirmation)
			newErrors.password_confirmation = "Passwords do not match";

		setErrors(newErrors);
		return Object.keys(newErrors).length === 0;
	};

	const handleSubmit = async (e) => {
		e.preventDefault();

		if (!validateForm()) return;

		setIsLoading(true);
		setErrors({});

		ApiService.fetchJson(
			route("auth.reset-password-request"),
			{
				email,
				token,
				password: formData.password,
				password_confirmation: formData.password_confirmation,
			},
			{
				method: "POST",
			},
		)
			.then(() => {
				setIsLoading(false);
				setIsSuccess(true);
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
				setIsLoading(false);
			});
	};

	const getPasswordStrength = () => {
		const password = formData.password;
		if (!password) return { strength: 0, label: "" };

		let strength = 0;
		if (password.length >= 8) strength++;
		if (/[a-z]/.test(password)) strength++;
		if (/[A-Z]/.test(password)) strength++;
		if (/\d/.test(password)) strength++;
		if (/[^a-zA-Z\d]/.test(password)) strength++;

		const labels = ["Very Weak", "Weak", "Fair", "Good", "Strong"];
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

	const passwordStrength = getPasswordStrength();

	// Loading state while validating token
	if (tokenValid === null) {
		return (
			<div className="min-h-screen flex items-center justify-center p-4">
				<Card className="w-full max-w-md">
					<CardContent className="p-8 text-center">
						<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-4"></div>
						<p className="text-muted-foreground">Validating reset link...</p>
					</CardContent>
				</Card>
			</div>
		);
	}

	// Invalid token state
	if (tokenValid === false) {
		return (
			<div className="min-h-screen flex items-center justify-center p-4">
				<Card className="w-full max-w-md">
					<CardHeader className="text-center">
						<div className="mx-auto mb-4 h-16 w-16 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
							<AlertCircle className="h-8 w-8 text-red-600 dark:text-red-400" />
						</div>
						<CardTitle className="text-2xl font-bold">Invalid reset link</CardTitle>
						<p className="text-muted-foreground">
							This password reset link is invalid or has expired.
						</p>
					</CardHeader>

					<CardContent className="space-y-4">
						<Button asChild className="w-full">
							<Link href="#!" onClick={modalStore.open("forgotPassword")}>
								Request new reset link
							</Link>
						</Button>

						<div className="text-center">
							<Link
								href="#!"
								onClick={modalStore.open("login")}
								className="text-sm text-primary hover:underline"
							>
								Back to login
							</Link>
						</div>
					</CardContent>
				</Card>
			</div>
		);
	}

	// Success state
	if (isSuccess) {
		return (
			<div className="min-h-screen flex items-center justify-center p-4">
				<Card className="w-full max-w-md">
					<CardHeader className="text-center">
						<div className="mx-auto mb-4 h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
							<CheckCircle className="h-8 w-8 text-green-600 dark:text-green-400" />
						</div>
						<CardTitle className="text-2xl font-bold">
							Password reset successful
						</CardTitle>
						<p className="text-muted-foreground">
							Your password has been successfully updated.
						</p>
					</CardHeader>

					<CardContent className="space-y-4">
						<Button asChild className="w-full">
							<Link href="#!" onClick={modalStore.open("login")}>
								<div className="flex items-center space-x-2">
									<span>Sign in with new password</span>
									<ArrowRight className="h-4 w-4" />
								</div>
							</Link>
						</Button>
					</CardContent>
				</Card>
			</div>
		);
	}

	return (
		<div className="min-h-screen flex items-center justify-center p-4">
			<Card className="w-full max-w-md">
				<CardHeader className="text-center">
					<div className="mx-auto mb-4 h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
						<span className="text-white font-bold text-xl">D</span>
					</div>
					<CardTitle className="text-2xl font-bold">Reset your password</CardTitle>
					<p className="text-muted-foreground">Enter your new password below</p>
				</CardHeader>

				<CardContent className="space-y-6">
					<form onSubmit={handleSubmit} className="space-y-4">
						{errors.general && (
							<div className="p-3 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
								<p className="text-sm text-red-600 dark:text-red-400">
									{errors.general}
								</p>
							</div>
						)}

						<div className="space-y-2">
							<Label htmlFor="email">Email</Label>
							<div className="relative">
								<Mail className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
								<Input
									id="email"
									type="email"
									disabled
									value={email}
									className="pl-10"
								/>
							</div>
							{errors.email && <p className="text-sm text-red-500">{errors.email}</p>}
						</div>

						<div className="space-y-2">
							<Label htmlFor="password">New Password</Label>
							<div className="relative">
								<Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4" />
								<Input
									id="password"
									type={showPassword ? "text" : "password"}
									placeholder="Enter your new password"
									value={formData.password}
									onChange={(e) => handleInputChange("password", e.target.value)}
									className={`pl-10 pr-10 ${errors.password ? "border-red-500" : ""}`}
								/>
								<Button
									type="button"
									variant="ghost"
									size="icon"
									className="absolute right-2 top-1/2 transform -translate-y-1/2 h-8 w-8"
									onClick={() => setShowPassword(!showPassword)}
								>
									{showPassword ? (
										<EyeOff className="h-4 w-4" />
									) : (
										<Eye className="h-4 w-4" />
									)}
								</Button>
							</div>

							{/* Password Strength Indicator */}
							{formData.password && (
								<div className="space-y-2">
									<div className="flex items-center justify-between text-xs">
										<span className="text-muted-foreground">
											Password strength
										</span>
										<span
											className={`font-medium ${passwordStrength.strength >= 3 ? "text-green-600" : "text-orange-600"}`}
										>
											{passwordStrength.label}
										</span>
									</div>
									<div className="w-full bg-gray-200 rounded-full h-1.5">
										<div
											className={`h-1.5 rounded-full transition-all duration-300 ${passwordStrength.color}`}
											style={{ width: `${passwordStrength.percentage}%` }}
										></div>
									</div>
								</div>
							)}

							{errors.password && (
								<p className="text-sm text-red-500">{errors.password}</p>
							)}
						</div>

						<div className="space-y-2">
							<Label htmlFor="password_confirmation">Confirm New Password</Label>
							<div className="relative">
								<Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4" />
								<Input
									id="password_confirmation"
									type={showConfirmPassword ? "text" : "password"}
									placeholder="Confirm your new password"
									value={formData.password_confirmation}
									onChange={(e) =>
										handleInputChange("password_confirmation", e.target.value)
									}
									className={`pl-10 pr-10 ${errors.password_confirmation ? "border-red-500" : ""}`}
								/>
								<Button
									type="button"
									variant="ghost"
									size="icon"
									className="absolute right-2 top-1/2 transform -translate-y-1/2 h-8 w-8"
									onClick={() => setShowConfirmPassword(!showConfirmPassword)}
								>
									{showConfirmPassword ? (
										<EyeOff className="h-4 w-4" />
									) : (
										<Eye className="h-4 w-4" />
									)}
								</Button>
							</div>
							{errors.password_confirmation && (
								<p className="text-sm text-red-500">
									{errors.password_confirmation}
								</p>
							)}
						</div>

						<Button type="submit" className="w-full" disabled={isLoading}>
							{isLoading ? (
								<div className="flex items-center space-x-2">
									<div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
									<span>Loading ...</span>
								</div>
							) : (
								<div className="flex items-center space-x-2">
									<span>Update password</span>
									<ArrowRight className="h-4 w-4" />
								</div>
							)}
						</Button>
					</form>

					<div className="text-center">
						<Link
							href="#!"
							onClick={modalStore.open("login")}
							className="text-sm text-primary hover:underline"
						>
							Back to login
						</Link>
					</div>
				</CardContent>
			</Card>
		</div>
	);
}
