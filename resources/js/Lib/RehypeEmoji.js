import { findAndReplace } from "hast-util-find-and-replace";
import { h } from "hastscript";

export default function rehypeEmoji(options) {
	const emojis = options.emojis || {};
	const match = options.match || /:([\w-]+):/g;
	const trim = options.trim || ((str) => str.slice(1, -1));

	const format =
		options.format ||
		((emoji, shortcode) =>
			h(
				"span",
				{
					class: "custom-emoji",
					title: `:${shortcode}:`,
				},
				emoji,
			));

	return (tree) =>
		findAndReplace(tree, [
			match,
			(str) => {
				const shortcode = trim(str);
				const emoji = emojis[shortcode];
				return emoji ? format(emoji, shortcode) : false;
			},
		]);
}
