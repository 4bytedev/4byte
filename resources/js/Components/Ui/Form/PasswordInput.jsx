import { forwardRef, useState } from "react";
import { Input } from "./Input";
import { Button } from "./Button";
import { Lock, Eye, EyeOff } from "lucide-react";
import { useTranslation } from "react-i18next";

export const PasswordInput = forwardRef(({ canShowPassword = true, ...props }, ref) => {
	const { t } = useTranslation();
	const [showPassword, setShowPassword] = useState(false);

	return (
		<>
			<div className="relative">
				<Lock className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
				<Input
					ref={ref}
					type={showPassword ? "text" : "password"}
					placeholder={t("Enter your password")}
					className="pl-10 pr-10"
					{...props}
				/>
				{canShowPassword && (
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
				)}
			</div>
		</>
	);
});

PasswordInput.displayName = "PasswordInput";
