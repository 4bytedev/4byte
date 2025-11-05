import { useMediaQuery } from "@uidotdev/usehooks";

export function useDevice() {
	const isMobile = useMediaQuery("only screen and (max-width: 768px)");
	const isTablet = useMediaQuery("only screen and (min-width: 769px) and (max-width: 1024px)");
	const isDesktop = useMediaQuery("only screen and (min-width: 1025px)");
	const isLandscape = useMediaQuery("(orientation: landscape)");
	const isPortrait = useMediaQuery("(orientation: portrait)");

	let type = "desktop";
	if (isMobile) type = "mobile";
	else if (isTablet) type = "tablet";

	const isTouchDevice =
		typeof window !== "undefined" && ("ontouchstart" in window || navigator.maxTouchPoints > 0);

	return {
		type,
		isMobile,
		isTablet,
		isDesktop,
		isLandscape,
		isPortrait,
		isTouchDevice,
	};
}
