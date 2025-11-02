import ApiService from "@/Services/ApiService";
import { useRef, useState } from "react";
import { ScrollArea } from "../Ui/ScrollArea";
import { Popover, PopoverContent, PopoverTrigger } from "../Ui/Popover";
import { Search } from "lucide-react";
import { Input } from "../Ui/Input";
import { ContentPreviewCard } from "./ContentPreviewCard";
import { useTranslation } from "react-i18next";
import { router } from "@inertiajs/react";

export function SearchBar({ isMobile = false }) {
	const [searchQuery, setSearchQuery] = useState("");
	const [searchResults, setSearchResults] = useState([]);
	const debounceRef = useRef(null);
	const { t } = useTranslation();

	const handleSearch = (query) => {
		if (isMobile) return;

		setSearchQuery(query);

		if (query.trim().length < 3) {
			setSearchResults([]);
			return;
		}

		if (debounceRef.current) clearTimeout(debounceRef.current);

		debounceRef.current = setTimeout(async () => {
			ApiService.fetchJson(
				route("api.search") + `?q=${query}`,
				{},
				{
					method: "GET",
				},
			).then((response) => {
				if (response && Array.isArray(response)) {
					setSearchResults(response);
				} else {
					setSearchResults([]);
				}
			});
		}, 500);
	};

	return (
		<div
			className={`flex items-center space-x-4 flex-1 ${isMobile ? "mb-3 block md:hidden" : "max-w-md mx-2 md:mx-8 hidden md:block"}`}
		>
			<div className="relative flex-1">
				<Popover
					open={searchQuery.trim().length >= 3 && searchResults.length > 0}
					onOpenChange={(open) => {
						if (!open) setSearchResults([]);
					}}
				>
					<PopoverTrigger asChild>
						<div>
							<Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4" />
							<Input
								placeholder={t("Search articles, news, users...")}
								value={searchQuery}
								onChange={(e) => handleSearch(e.target.value)}
								onKeyDown={(e) => {
									if (e.key === "Enter" && searchQuery.trim().length >= 3) {
										if (debounceRef.current) {
											clearTimeout(debounceRef.current);
											debounceRef.current = null;
										}
										e.preventDefault();
										setSearchResults([]);
										router.visit(route("search.view", { q: searchQuery }), {
											method: "get",
										});
									}
								}}
								className="pl-10"
							/>
						</div>
					</PopoverTrigger>

					<PopoverContent
						side="bottom"
						align="start"
						sideOffset={6}
						className="w-[28rem] p-0 shadow-lg border bg-background"
						onInteractOutside={() => setSearchResults([])}
						forceMount
					>
						<ScrollArea className="max-h-96 p-1">
							{searchQuery.trim().length >= 3 ? (
								searchResults.length > 0 ? (
									searchResults.map((item, idx) => (
										<ContentPreviewCard key={idx} item={item} />
									))
								) : (
									<div className="p-4 text-center text-muted-foreground">
										{t("No results found")}
									</div>
								)
							) : (
								<div className="p-4 text-center text-muted-foreground">
									{t("Type at least 3 characters to search")}
								</div>
							)}
						</ScrollArea>
					</PopoverContent>
				</Popover>
			</div>
		</div>
	);
}
