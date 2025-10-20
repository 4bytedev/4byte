import { useState } from "react";
import { Card, CardContent, CardFooter } from "@/Components/Ui/Card";
import { Textarea } from "@/Components/Ui/Textarea";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Separator } from "@/Components/Ui/Separator";
import {
	DropdownMenu,
	DropdownMenuContent,
	DropdownMenuItem,
	DropdownMenuTrigger,
} from "@/Components/Ui/DropdownMenu";
import { ArrowRight, Image, Pencil } from "lucide-react";
import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { useAuthStore } from "@/Stores/AuthStore";
import { Button } from "@/Components/Ui/Button";
import ApiService from "@/Services/ApiService";
import { Label } from "../Ui/Label";
import { ImageSlider } from "@/Components/Ui/ImageSlider";
import { toast } from "@/Hooks/useToast";

export function CreatorCard() {
	const [content, setContent] = useState("");
	const [errors, setErrors] = useState({});
	const [medias, setMedias] = useState([]);
	const [expanded, setExpanded] = useState(false);
	const { t } = useTranslation();
	const authStore = useAuthStore();

	const handleSubmit = () => {
		setErrors({});

		if (content.length <= 50 && medias.length < 0) {
			setErrors({ content: t("Content must be at least 50 characters") });
			return;
		}

		const data = {
			content: content,
		};

		if (medias.length > 0) {
			medias.forEach((media, index) => {
				data[`media[${index}]`] = media.file;
			});
		}

		ApiService.fetchJson(route("api.entry.crud.create"), data, { isMultipart: true })
			.then(() => {
				toast({
					title: t("Successfully!"),
					description: t(
						"Your entry is live! It might take a moment to show up, thanks for your patience.",
					),
				});
				setContent("");
				setMedias([]);
				setExpanded(false);
			})
			.catch((response) => {
				setErrors(response.errors || { general: "Invalid credentials. Please try again." });
			});
	};

	return (
		<>
			{expanded && (
				<div
					className="fixed inset-0 bg-black/50 z-40"
					onClick={() => setExpanded(false)}
				/>
			)}

			<Card
				className={`mb-4 z-50 ${
					expanded
						? "fixed top-[250px] left-1/2 w-full max-w-2xl -translate-x-1/2 -translate-y-1/2 shadow-2xl"
						: "relative"
				}`}
			>
				<CardContent className="p-2">
					<div className="flex items-center gap-3 mb-2">
						<Avatar className="h-10 w-10">
							<AvatarImage src={authStore.user.avatar || "/placeholder-avatar.jpg"} />
							<AvatarFallback>
								{authStore.user.name
									.split(" ")
									.map((n) => n[0])
									.join("") || "U"}
							</AvatarFallback>
						</Avatar>
						<div className="w-full">
							<Textarea
								placeholder={t("What do you thinking?")}
								rows={expanded ? 5 : 3}
								onFocus={() => setExpanded(true)}
								value={content}
								onChange={(e) => setContent(e.target.value)}
							/>
							{errors.content && (
								<p className="text-sm text-red-500">{errors.content}</p>
							)}
						</div>
					</div>
					{medias.length > 0 && expanded && (
						<ImageSlider
							medias={medias}
							onRemove={(id) => setMedias((prev) => prev.filter((m) => m.id !== id))}
							spaceBetween={8}
							slidesPerView={4}
							className="h-16"
						/>
					)}
				</CardContent>

				<CardContent className="py-0">
					<Separator />
				</CardContent>
				<CardFooter className="flex py-3 justify-between">
					<div className="flex gap-2">
						<DropdownMenu>
							<DropdownMenuTrigger className="flex w-full h-full items-center">
								<Button variant="ghost" className="px-2 py-0" asChild>
									<Pencil className="w-4 h-4 mr-2" />
									{t("Write")}
								</Button>
							</DropdownMenuTrigger>
							<DropdownMenuContent align="start" className="w-48">
								<DropdownMenuItem asChild>
									<Link
										href={route("article.crud.create.view")}
										className="flex w-full h-full"
									>
										{t("Article")}
									</Link>
								</DropdownMenuItem>
							</DropdownMenuContent>
						</DropdownMenu>
						<Button variant="ghost" disabled={!expanded}>
							<Label
								htmlFor="creator-media-input"
								className="px-2 py-0 flex items-center cursor-pointer w-full h-full"
							>
								<Image className="w-4 h-4 mr-2" />
								{t("Media")}
							</Label>
							<input
								type="file"
								accept="image/*"
								id="creator-media-input"
								hidden
								multiple
								onChange={(e) => {
									const files = Array.from(e.target.files);

									if (files.length === 0) return;

									setMedias((prev) => {
										const existingIds = new Set(prev.map((item) => item.id));

										const newFiles = files
											.map((file) => ({
												image: URL.createObjectURL(file),
												file,
												id: `${file.name}_${file.size}_${file.lastModified}`,
											}))
											.filter((fileObj) => !existingIds.has(fileObj.id));

										return [...prev, ...newFiles];
									});
								}}
							/>
						</Button>
					</div>
					<Button
						variant="ghost"
						className="px-2 py-0"
						disabled={!expanded || (content.length <= 50 && medias.length === 0)}
						onClick={handleSubmit}
					>
						<ArrowRight className="w-4 h-4 mr-2" />
						Gönder
					</Button>
				</CardFooter>
			</Card>
		</>
	);
}

export default CreatorCard;
