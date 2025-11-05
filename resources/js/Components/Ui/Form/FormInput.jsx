import { FormControl, FormItem, FormLabel, FormMessage } from "./Form";
import { Input } from "./Input";

export function FormInput({ icon: Icon, placeholder, label, field, ...props }) {
	return (
		<FormItem>
			<FormLabel>{label}</FormLabel>
			<FormControl>
				<div className="relative">
					{Icon && (
						<Icon className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground h-4 w-4" />
					)}
					<Input
						placeholder={placeholder}
						className={Icon ? "pl-10" : ""}
						{...field}
						{...props}
					/>
				</div>
			</FormControl>
			<FormMessage />
		</FormItem>
	);
}
