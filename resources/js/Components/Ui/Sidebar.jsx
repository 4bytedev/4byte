import * as React from "react";
import { cn } from "@/Lib/Utils";
import { useSidebar } from "@/Contexts/SidebarContext";

const SidebarRoot = React.forwardRef(({ className, children, ...props }, ref) => {
	const { isOpen } = useSidebar();

	return (
		<aside
			ref={ref}
			className={cn(
				`fixed top-0 left-0 z-100 h-full bg-background border-r shadow-lg transform transition-transform duration-300 md:translate-x-0 md:relative`,
				isOpen ? "translate-x-0" : "-translate-x-full",
				className,
			)}
			{...props}
		>
			{children}
		</aside>
	);
});
SidebarRoot.displayName = "SidebarRoot";

const SidebarOverlay = React.forwardRef(({ className, onClick, ...props }, ref) => {
	const { isOpen, toggleSidebar } = useSidebar();

	return (
		<div
			ref={ref}
			onClick={onClick || toggleSidebar}
			className={cn(
				`fixed inset-0 bg-black/30 z-75 transition-opacity duration-300 md:hidden`,
				isOpen ? "opacity-100" : "opacity-0 pointer-events-none",
				className,
			)}
			{...props}
		/>
	);
});
SidebarOverlay.displayName = "SidebarOverlay";

export { SidebarRoot, SidebarOverlay };
