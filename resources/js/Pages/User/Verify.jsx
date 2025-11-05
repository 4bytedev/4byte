import { useState, useEffect } from "react";
import { Mail, CheckCircle, AlertCircle, RefreshCw } from "lucide-react";
import { Button } from "@/Components/Ui/Form/Button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { useAuthStore } from "@/Stores/AuthStore";
import ApiService from "@/Services/ApiService";
import { Trans, useTranslation } from "react-i18next";

export default function VerifyPage() {
	const [isLoading, setIsLoading] = useState(false);
	const [isVerified, setIsVerified] = useState(false);
	const [errors, setErrors] = useState({});
	const [resendCooldown, setResendCooldown] = useState(0);
	const [redirectCountdown, setRedirectCountdown] = useState(5);
	const authStore = useAuthStore();
	const { t } = useTranslation();

	useEffect(() => {
		if (authStore.isAuthenticated && authStore.user.verified) {
			setIsVerified(true);
		}
	}, [authStore.user]);

	useEffect(() => {
		if (resendCooldown > 0) {
			const timer = setTimeout(() => setResendCooldown(resendCooldown - 1), 1000);
			return () => clearTimeout(timer);
		}
	}, [resendCooldown]);

	useEffect(() => {
		if (resendCooldown > 0) {
			const timer = setTimeout(() => setResendCooldown(resendCooldown - 1), 1000);
			return () => clearTimeout(timer);
		}
	}, [resendCooldown]);

	useEffect(() => {
		if (isVerified && redirectCountdown > 0) {
			const timer = setTimeout(() => setRedirectCountdown(redirectCountdown - 1), 1000);
			return () => clearTimeout(timer);
		}

		if (isVerified && redirectCountdown === 0) {
			window.location.href = "/";
		}
	}, [isVerified, redirectCountdown]);

	const handleResendLink = async () => {
		if (resendCooldown > 0) return;

		setIsLoading(true);

		ApiService.fetchJson(
			route("api.user.verification.resend"),
			{},
			{
				method: "POST",
			},
		)
			.then(() => {
				setResendCooldown(60);
				setErrors({});
				setIsLoading(false);
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Please try again later." });
				setIsLoading(false);
			});
	};

	if (isVerified) {
		return (
			<div className="min-h-screen flex items-center justify-center p-4">
				<Card className="w-full max-w-md">
					<CardHeader className="text-center">
						<div className="mx-auto mb-4 h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
							<CheckCircle className="h-8 w-8 text-green-600 dark:text-green-400" />
						</div>
						<CardTitle className="text-2xl font-bold">{t("Email verified!")}</CardTitle>
						<p className="text-muted-foreground">
							{t(
								"Your email has been successfully verified. You can now access all features.",
							)}
						</p>
						<p className="text-muted-foreground">
							<Trans
								i18nKey="redirect_in"
								values={{ count: redirectCountdown }}
								components={{ strong: <strong /> }}
							/>
						</p>
					</CardHeader>
				</Card>
			</div>
		);
	}

	return (
		<div className="min-h-screen flex items-center justify-center p-4">
			<Card className="w-full max-w-md">
				<CardHeader className="text-center">
					<div className="mx-auto mb-4 h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
						<Mail className="h-8 w-8 text-blue-600 dark:text-blue-400" />
					</div>
					<CardTitle className="text-2xl font-bold">{t("Verify your email")}</CardTitle>
					<p className="text-muted-foreground">
						{t("Verify your email by following the link in your email")}
					</p>
				</CardHeader>

				<CardContent className="space-y-6">
					{errors.general && (
						<div className="p-3 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 flex items-center space-x-2">
							<AlertCircle className="h-4 w-4 text-red-600 dark:text-red-400" />
							<p className="text-sm text-red-600 dark:text-red-400">
								{errors.general}
							</p>
						</div>
					)}

					<div className="text-center space-y-4">
						<p className="text-sm text-muted-foreground">
							{t("Didn't receive the email?")}
						</p>

						<Button
							variant="outline"
							onClick={handleResendLink}
							disabled={isLoading || resendCooldown > 0}
							className="w-full"
						>
							{isLoading ? (
								<div className="flex items-center space-x-2">
									<div className="animate-spin rounded-full h-4 w-4 border-b-2 border-current"></div>
									<span>{t("Sending...")}</span>
								</div>
							) : resendCooldown > 0 ? (
								<div className="flex items-center space-x-2">
									<RefreshCw className="h-4 w-4 mr-1" />
									<Trans
										i18nKey="resend_in"
										values={{ count: resendCooldown }}
										components={{ strong: <strong /> }}
									/>
								</div>
							) : (
								<div className="flex items-center space-x-2">
									<RefreshCw className="h-4 w-4" />
									<span>{t("Resend link")}</span>
								</div>
							)}
						</Button>
					</div>
				</CardContent>
			</Card>
		</div>
	);
}
