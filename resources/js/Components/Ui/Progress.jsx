import React from "react";
import { cn } from "@/Lib/Utils";

const Progress = React.forwardRef(
	(
		{
			className,
			value = 0,
			max = 100,
			variant = "default",
			size = "default",
			showLabel = false,
			label,
			animated = false,
			...props
		},
		ref,
	) => {
		const percentage = Math.min(Math.max((value / max) * 100, 0), 100);

		const getVariantClasses = () => {
			switch (variant) {
				case "success":
					return "bg-green-500";
				case "warning":
					return "bg-yellow-500";
				case "error":
					return "bg-red-500";
				default:
					return "bg-primary";
			}
		};

		const getSizeClasses = () => {
			switch (size) {
				case "sm":
					return "h-1";
				case "lg":
					return "h-4";
				default:
					return "h-2";
			}
		};

		return (
			<div className="w-full space-y-1">
				{showLabel && (
					<div className="flex justify-between text-sm">
						<span className="text-muted-foreground">{label}</span>
						<span className="text-muted-foreground">{Math.round(percentage)}%</span>
					</div>
				)}
				<div
					ref={ref}
					className={cn(
						"relative w-full overflow-hidden rounded-full bg-secondary",
						getSizeClasses(),
						className,
					)}
					{...props}
				>
					<div
						className={cn(
							"h-full w-full flex-1 transition-all duration-300 ease-in-out",
							getVariantClasses(),
							animated && "relative overflow-hidden",
						)}
						style={{ transform: `translateX(-${100 - percentage}%)` }}
					>
						{animated && (
							<div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer" />
						)}
					</div>
				</div>
			</div>
		);
	},
);

Progress.displayName = "Progress";

export { Progress };
