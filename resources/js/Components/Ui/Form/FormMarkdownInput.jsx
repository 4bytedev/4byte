import { MarkdownEditor } from "@/Components/Common/MarkdownEditor";
import { FormControl, FormItem, FormLabel, FormMessage } from "./Form";

export function FormMarkdownInput({ placeholder, label, field }) {
	return (
		<FormItem>
			<FormLabel>{label}</FormLabel>
			<FormControl>
				<MarkdownEditor
					textareaProps={{
						placeholder,
					}}
					value={field.value}
					onChange={field.onChange}
				/>
			</FormControl>
			<FormMessage />
		</FormItem>
	);
}
