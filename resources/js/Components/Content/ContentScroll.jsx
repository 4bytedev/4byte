import { useState, useEffect, useCallback, useRef } from "react";
import { Grid, List } from "lucide-react";
import { Button } from "@/Components/Ui/Button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Components/Ui/Tabs";
import { useTranslation } from "react-i18next";
import { CreatorCard } from "./CreatorCard";
import { ContentCard } from "./ContentCard";

const ContentScroll = ({
	endpoint,
	initialTab = "all",
	tabs = [{ value: "all", label: "All" }],
	filters = [],
	title = false,
	description = false,
	creator = false,
}) => {
	const [viewMode, setViewMode] = useState("grid");
	const [activeTab, setActiveTab] = useState(initialTab);
	const [content, setContent] = useState([]);
	const [contentCache, setContentCache] = useState({});
	const [loading, setLoading] = useState(false);
	const [error, setError] = useState(null);
	const [, setPage] = useState(1);
	const [hasMore, setHasMore] = useState(true);
	const observer = useRef();
	const { t } = useTranslation();

	const fetchContent = async (tab = activeTab, pageOverride = 1) => {
		const cached = contentCache[tab];

		if (pageOverride === 1 && cached) {
			setContent(cached.items);
			setHasMore(cached.hasMore);
			return;
		}

		if (pageOverride === 1) {
			setContent([]);
		}

		if (loading) return;

		setLoading(true);
		setError(null);

		try {
			const url = new URL(endpoint);
			url.searchParams.append("page", pageOverride);

			if (tab !== "all") {
				url.searchParams.append("tab", tab);
			}

			Object.keys(filters).forEach((filter) => {
				url.searchParams.append(filter, filters[filter]);
			});

			const response = await fetch(url);
			if (!response.ok) {
				setHasMore(false);
				return;
			}

			const data = await response.json();
			const items = data.items || data.content || data;

			const newContent = pageOverride === 1 ? items : [...(cached?.items || []), ...items];

			const more = items.length == 10;

			setContent(newContent);
			setHasMore(more);

			setContentCache((prev) => ({
				...prev,
				[tab]: { items: newContent, hasMore: more },
			}));
		} catch (err) {
			setError(err.message);
			console.error("Failed to fetch content:", err);
		} finally {
			setLoading(false);
		}
	};

	useEffect(() => {
		setPage(1);
		fetchContent(activeTab, 1);
	}, [activeTab]);

	const lastItemRef = useCallback(
		(node) => {
			if (loading) return;
			if (observer.current) observer.current.disconnect();

			observer.current = new IntersectionObserver(
				(entries) => {
					if (entries[0].isIntersecting && hasMore) {
						setPage((prevPage) => {
							const nextPage = prevPage + 1;
							fetchContent(activeTab, nextPage);
							return nextPage;
						});
					}
				},
				{
					rootMargin: "50px",
				},
			);

			if (node) observer.current.observe(node);
		},
		[loading, hasMore, activeTab],
	);

	const handleTabChange = (value) => {
		setActiveTab(value);
	};

	return (
		<main className="flex-1 p-3 sm:6-6">
			<div className="max-w-6xl mx-auto">
				{(title || description) && (
					<div className="mb-8">
						{title && <h1 className="text-3xl font-bold mb-2">{title}</h1>}
						{description && <p className="text-muted-foreground">{description}</p>}
					</div>
				)}

				{creator && (
					<>
						<CreatorCard />
					</>
				)}

				<div className="flex items-center justify-between mb-6">
					<Tabs
						defaultValue={initialTab}
						onValueChange={handleTabChange}
						className="w-full"
					>
						<div className="flex items-center justify-between">
							<TabsList className="grid w-full max-w-md grid-cols-5">
								{tabs.map((tab) => (
									<TabsTrigger key={tab.value} value={tab.value}>
										{tab.label}
									</TabsTrigger>
								))}
							</TabsList>

							<div className="flex items-center space-x-2">
								{/* {filters.length > 0 && (
                    <DropdownMenu>
                      <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="sm">
                          <Filter className="h-4 w-4 mr-1" />
                          Filters
                        </Button>
                      </DropdownMenuTrigger>
                      <DropdownMenuContent align="end" className="w-56 p-2">
                        {filters.map(f => (
                          <DropdownMenuItem key={f.slug} className="flex flex-col py-2" closeOnSelect={false}>
                            <Label htmlFor={`filter-${f.slug}`}>{f.label}</Label>
                            {f.type === 'select' && (
                              <div className="space-y-2 mt-1">
                                <Select value={filterValues[f.slug]} onValueChange={value => handleFilterChange(f.slug, value)}>
                                  <SelectTrigger>
                                    <SelectValue placeholder="Select category" />
                                  </SelectTrigger>
                                  <SelectContent className="z-[1000]">
                                    {f.options.map((opt) => (
                                      <SelectItem key={opt.value} value={opt.value}>
                                        {opt.label}
                                      </SelectItem>
                                    ))}
                                  </SelectContent>
                                </Select>
                              </div>
                            )}
                          </DropdownMenuItem>
                        ))}
                      </DropdownMenuContent>
                    </DropdownMenu>
                  )} */}
								<div className="items-center border rounded-lg p-1 hidden sm:flex">
									<Button
										variant={viewMode === "grid" ? "default" : "ghost"}
										size="sm"
										onClick={() => setViewMode("grid")}
									>
										<Grid className="h-4 w-4" />
									</Button>
									<Button
										variant={viewMode === "list" ? "default" : "ghost"}
										size="sm"
										onClick={() => setViewMode("list")}
									>
										<List className="h-4 w-4" />
									</Button>
								</div>
							</div>
						</div>

						{tabs.map((tab) => (
							<TabsContent key={tab.value} value={tab.value} className="mt-6">
								{error && (
									<div className="text-center py-8 text-destructive">
										<p>{t("Error loading content")}</p>
										<Button
											onClick={() => fetchContent(1, activeTab, true)}
											className="mt-4"
										>
											{t("Try Again")}
										</Button>
									</div>
								)}

								{!error && content.length === 0 && !loading && (
									<div className="text-center py-12">
										<p className="text-muted-foreground">
											{t("No content found")}
										</p>
									</div>
								)}

								<div
									className={`grid gap-6 ${viewMode === "grid" ? "grid-cols-1" : "grid-cols-2"}`}
								>
									{content.map((item, index) => {
										return (
											<ContentCard
												key={item.slug || index}
												{...item}
												showImage={viewMode === "grid"}
												userFooter={viewMode === "list"}
											/>
										);
									})}
								</div>

								<div ref={lastItemRef} className="flex justify-center py-8">
									{loading && (
										<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
									)}
								</div>
							</TabsContent>
						))}
					</Tabs>
				</div>
			</div>
		</main>
	);
};

export default ContentScroll;
