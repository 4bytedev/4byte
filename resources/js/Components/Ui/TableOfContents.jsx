import React from "react";
import {
	Breadcrumb,
	BreadcrumbList,
	BreadcrumbItem,
	BreadcrumbLink,
} from "@/Components/Ui/BreadCrumb";
import { Card, CardContent, CardHeader, CardTitle } from "./Card";
import { useTranslation } from "react-i18next";
import { slugify } from "@/Lib/Utils";

export default function TableOfContents({ markdown, ...props }) {
	const { t } = useTranslation();
	const headings = [...markdown.matchAll(/^(#{2,6})\s+(.*)$/gm)].map((m) => ({
		level: m[1].length,
		text: m[2],
	}));
	if (headings.length < 1) {
		return <></>;
	}
	return (
		<Card {...props}>
			<CardHeader>
				<CardTitle>{t("Contents")}</CardTitle>
			</CardHeader>
			<CardContent>
				<Breadcrumb>
					<BreadcrumbList className="flex-col items-start gap-1 text-md">
						{headings.map((h, i) => (
							<BreadcrumbItem key={i} style={{ marginLeft: `${h.level - 2}rem` }}>
								<BreadcrumbLink href={`#${slugify(h.text)}`}>
									{h.text}
								</BreadcrumbLink>
							</BreadcrumbItem>
						))}
					</BreadcrumbList>
				</Breadcrumb>
			</CardContent>
		</Card>
	);
}
