import * as React from "react";
import { cn } from "@/Lib/Utils";
import MarkdownRenderer from "./MarkdownRenderer";

const getClosedBanners = () => {
	try {
		const stored = localStorage.getItem("kenepa-banners::closed");
		return stored ? JSON.parse(stored) : [];
	} catch (error) {
		console.error("Error reading from localStorage:", error);
		return [];
	}
};

const setClosedBanner = (id) => {
	try {
		const closedBanners = getClosedBanners();
		if (!closedBanners.includes(id)) {
			localStorage.setItem("kenepa-banners::closed", JSON.stringify([...closedBanners, id]));
		}
	} catch (error) {
		console.error("Error writing to localStorage:", error);
	}
};

const Banner = React.forwardRef(
	(
		{
			className,
			variant = "default",
			show = true,
			onClose,
			canClose = true,
			linkActive = false,
			linkNewTab = true,
			linkUrl = "#",
			id,
			children,
			...props
		},
		ref,
	) => {
		const [isVisible, setIsVisible] = React.useState(show);

		React.useEffect(() => {
			if (id) {
				const closedBanners = getClosedBanners();
				setIsVisible(show && !closedBanners.includes(id));
			} else {
				setIsVisible(show);
			}
		}, [show, id]);

		if (!isVisible) return null;

		const handleClose = (e) => {
			e.preventDefault();
			e.stopPropagation();

			if (id) {
				setClosedBanner(id);
			}

			setIsVisible(false);
			if (onClose) onClose();
		};

		const variants = {
			default: "bg-background",
			blue: "bg-blue-800/50 text-blue-100",
			green: "bg-green-800/50 text-green-100",
			red: "bg-red-800/50 text-red-100",
		};

		const BannerContent = (
			<>
				{children}

				{/* Close Button */}
				{canClose && (
					<div className="flex flex-1 justify-end">
						<button
							type="button"
							onClick={handleClose}
							className="-m-3 p-3 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
						>
							<span className="sr-only">Dismiss</span>
							<svg
								viewBox="0 0 20 20"
								fill="currentColor"
								aria-hidden="true"
								className="h-5 w-5"
							>
								<path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
							</svg>
						</button>
					</div>
				)}
			</>
		);

		const commonClasses = cn(
			"relative isolate flex items-center gap-x-6 overflow-hidden px-6 py-2.5 after:pointer-events-none after:absolute after:inset-x-0 after:bottom-0 after:h-px after:bg-white/10 sm:px-3.5 sm:before:flex-1",
			variants[variant],
			className,
		);

		if (linkActive) {
			return (
				<a
					ref={ref}
					href={linkUrl}
					target={linkNewTab ? "_blank" : "_self"}
					rel={linkNewTab ? "noopener noreferrer" : undefined}
					className={commonClasses}
					{...props}
				>
					{BannerContent}
				</a>
			);
		}

		return (
			<div ref={ref} className={commonClasses} {...props}>
				{BannerContent}
			</div>
		);
	},
);
Banner.displayName = "Banner";

const BannerContent = React.forwardRef(({ className, children, ...props }, ref) => {
	return (
		<div
			ref={ref}
			className={cn("flex flex-wrap items-center gap-x-4 gap-y-2", className)}
			{...props}
		>
			{children}
		</div>
	);
});
BannerContent.displayName = "BannerContent";

const BannerText = React.forwardRef(({ className, children, ...props }, ref) => {
	return (
		<div ref={ref} className={cn("text-sm/6", className)} {...props}>
			{children}
		</div>
	);
});
BannerText.displayName = "BannerText";

const BannerAction = React.forwardRef(({ className, children, ...props }, ref) => {
	return (
		<a
			ref={ref}
			className={cn(
				"flex-none rounded-full bg-white/10 px-3.5 py-1 text-sm font-semibold shadow-xs hover:bg-white/15 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white",
				className,
			)}
			{...props}
		>
			{children}
		</a>
	);
});
BannerAction.displayName = "BannerAction";

const BannerMap = React.forwardRef(({ banners }) => {
	return (
		<>
			{banners.map((banner) => {
				const data = banner.data;
				return (
					<Banner
						id={data.id}
						key={data.id}
						linkActive={
							data.link_active && data.link_click_action == "clickable_banner"
						}
						linkNewTab={data.link_open_in_new_tab}
						linkUrl={data.link_url}
						canClose={data.can_be_closed_by_user}
						onClose={() => console.log("Banner closed")}
					>
						<BannerContent>
							<BannerText>
								<MarkdownRenderer content={data.content} />
							</BannerText>
							{data.link_active && data.link_click_action == "button" && (
								<BannerAction
									target={data.link_open_in_new_tab ? "_blank" : "_self"}
									href={data.link_url}
								>
									{data.link_text}
								</BannerAction>
							)}
						</BannerContent>
					</Banner>
				);
			})}
		</>
	);
});
BannerMap.displayName = "BannerMap";

export { Banner, BannerContent, BannerText, BannerAction, BannerMap };
