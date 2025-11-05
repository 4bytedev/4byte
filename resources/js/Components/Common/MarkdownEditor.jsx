import MDEditor from "@uiw/react-md-editor";
import rehypeSanitize from "rehype-sanitize";

export function MarkdownEditor({ onChange, value, ...props }) {
	return (
		<div className="container">
			<MDEditor
				value={value}
				onChange={onChange}
				className={"!bg-background"}
				previewOptions={{
					rehypePlugins: [[rehypeSanitize]],
				}}
				height={"800px"}
				{...props}
			/>
		</div>
	);
}
