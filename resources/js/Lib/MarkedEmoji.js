import Emoji from "@/Data/Emoji";

export function markedEmoji(marked) {
	const emojis = Emoji;
	const match = /^:([\w-]+):/;
	const trim = (str) => str.slice(1, -1);
	const format = (emoji, shortcode) =>
		`<span class="custom-emoji" title=":${shortcode}:">${emoji}</span>`;

	return marked.use({
		extensions: [
			{
				name: "emoji",
				level: "inline",
				start(src) {
					return src.match(/:/)?.index;
				},
				tokenizer(src) {
					const matchResult = match.exec(src);

					if (matchResult) {
						const shortcode = trim(matchResult[0]);

						const emoji = emojis[shortcode];
						if (emoji) {
							return {
								type: "emoji",
								raw: matchResult[0],
								emoji,
								shortcode,
							};
						}
					}
				},
				renderer(token) {
					return format(token.emoji, token.shortcode);
				},
			},
		],
	});
}
