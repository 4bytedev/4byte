import React, { useState, useRef, useEffect } from "react";
import { cn } from "@/Lib/Utils";

const DropdownMenu = ({ onClick, children, ...props }) => {
	const [isOpen, setIsOpen] = useState(false);
	const dropdownRef = useRef(null);

	useEffect(() => {
		const handleClickOutside = (event) => {
			if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
				setIsOpen(false);
			}
		};

		document.addEventListener("mousedown", handleClickOutside);
		return () => document.removeEventListener("mousedown", handleClickOutside);
	}, []);

	return (
		<div ref={dropdownRef} {...props} className="relative">
			{React.Children.map(children, (child) => {
				if (child.type === DropdownMenuTrigger) {
					return React.cloneElement(child, {
						onClick: () => {
							setIsOpen(!isOpen);
							if (onClick) onClick();
						},
						isOpen,
					});
				}
				if (child.type === DropdownMenuContent) {
					return React.cloneElement(child, {
						isOpen,
						onClose: () => setIsOpen(false),
					});
				}
				return child;
			})}
		</div>
	);
};

const DropdownMenuTrigger = React.forwardRef(
	({ className, children, onClick, asChild, isOpen, ...props }, ref) => {
		const Comp = asChild ? "span" : "button";

		return (
			<Comp
				ref={ref}
				data-is-open={isOpen}
				className={className}
				onClick={onClick}
				{...props}
			>
				{children}
			</Comp>
		);
	},
);
DropdownMenuTrigger.displayName = "DropdownMenuTrigger";

const DropdownMenuContent = React.forwardRef(
	({ className, children, isOpen, onClose, align = "center", ...props }, ref) => {
		if (!isOpen) return null;

		const alignmentClasses = {
			start: "left-0",
			center: "left-1/2 transform -translate-x-1/2",
			end: "right-0",
		};

		return (
			<div
				ref={ref}
				className={cn(
					"absolute top-full z-50 mt-1 min-w-[8rem] overflow-visible rounded-md border bg-popover p-1 text-popover-foreground shadow-md animate-in fade-in-0 zoom-in-95",
					alignmentClasses[align],
					className,
				)}
				{...props}
			>
				{React.Children.map(children, (child) => {
					if (child.type === DropdownMenuItem) {
						return React.cloneElement(child, { onClose });
					}
					return child;
				})}
			</div>
		);
	},
);
DropdownMenuContent.displayName = "DropdownMenuContent";

const DropdownMenuItem = React.forwardRef(
	({ className, children, onClick, onClose, closeOnSelect = true, asChild, ...props }, ref) => {
		const handleClick = (e) => {
			if (onClick) onClick(e);
			if (onClose && closeOnSelect) onClose();
		};

		const Comp = asChild ? "span" : "div";
		return (
			<Comp
				ref={ref}
				className={cn(
					"relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground",
					className,
				)}
				onClick={handleClick}
				{...props}
			>
				{children}
			</Comp>
		);
	},
);
DropdownMenuItem.displayName = "DropdownMenuItem";

const DropdownMenuSeparator = React.forwardRef(({ className, ...props }, ref) => (
	<div ref={ref} className={cn("-mx-1 my-1 h-px bg-muted", className)} {...props} />
));
DropdownMenuSeparator.displayName = "DropdownMenuSeparator";

export {
	DropdownMenu,
	DropdownMenuTrigger,
	DropdownMenuContent,
	DropdownMenuItem,
	DropdownMenuSeparator,
};
