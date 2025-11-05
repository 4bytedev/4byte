import { Lock } from "lucide-react";
import { FormControl, FormItem, FormLabel, FormMessage } from "./Form";
import { PasswordInput } from "./PasswordInput";
import { PasswordStrength } from "./PasswordStrength";

export function FormPasswordInput({ label, passwordStrength, field, ...props }) {
	return (
		<FormItem>
			<FormLabel>{label}</FormLabel>
			<FormControl>
				<div className="relative">
					<Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
					<PasswordInput {...field} {...props} />
				</div>
			</FormControl>
			<FormMessage />
			{passwordStrength && <PasswordStrength value={passwordStrength} />}
		</FormItem>
	);
}
