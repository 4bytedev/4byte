import React, { useEffect } from "react";
import { marked } from "marked";
import hljs from "highlight.js";
import parse from "html-react-parser";
import { useTranslation } from "react-i18next";
import { slugify } from "@/Lib/Utils";
import { markedCodeGroup, initCodeGroups } from "@/Lib/MarkedCodeGroup";
import { markedEmoji } from "@/Lib/MarkedEmoji";

export default function MarkdownRenderer({ content }) {
	const { t } = useTranslation();
	const renderer = new marked.Renderer();

	renderer.heading = function (text) {
		const id = slugify(text.text);

		return `<h${text.depth} id="${id}">
              <a href="#${id}" class="no-underline relative before:content-[''] before:absolute before:bottom-0 before:left-0 before:w-0 before:h-[2px] before:bg-foreground before:transition-all hover:before:w-full">${text.text}</a>
            </h${text.depth}>`;
	};

	markedCodeGroup(marked);
	markedEmoji(marked);

	const html = marked(content, {
		renderer,
	});

	useEffect(() => {
		initCodeGroups();
		document.querySelectorAll("pre code").forEach((block) => {
			document.querySelectorAll("pre code").forEach((block) => {
				if (!block.dataset.highlighted) {
					hljs.highlightElement(block);
				}
			});

			if (!block.parentElement.querySelector(".copy-btn")) {
				const btn = document.createElement("button");
				btn.innerText = t("Copy");
				btn.className =
					"copy-btn absolute top-2 right-2 bg-gray-800 text-white text-xs px-2 py-1 rounded hover:bg-gray-700";
				btn.onclick = () => {
					navigator.clipboard.writeText(block.innerText);
					btn.innerText = t("Copied!");
					setTimeout(() => (btn.innerText = t("Copy")), 1500);
				};

				block.parentElement.style.position = "relative";
				block.parentElement.appendChild(btn);
			}
		});
	}, [content]);

	return <div className="prose dark:prose-invert max-w-none">{parse(html)}</div>;
}
