import * as React from "react";
import * as DialogPrimitive from "@radix-ui/react-dialog";
import { cn } from "@/Lib/Utils";
import { Button } from "@/Components/Ui/Form/Button";

const Modal = React.forwardRef(({ ...props }, ref) => (
	<DialogPrimitive.Root {...props} ref={ref} />
));
Modal.displayName = "Modal";

const ModalTrigger = React.forwardRef(({ className, children, ...props }, ref) => (
	<DialogPrimitive.Trigger asChild>
		<Button ref={ref} className={className} {...props}>
			{children}
		</Button>
	</DialogPrimitive.Trigger>
));
ModalTrigger.displayName = DialogPrimitive.Trigger.displayName;

const ModalOverlay = React.forwardRef(({ className, ...props }, ref) => (
	<DialogPrimitive.Overlay
		ref={ref}
		style={{ zIndex: 999 }}
		className={cn("fixed inset-0 bg-black/50", className)}
		{...props}
	/>
));
ModalOverlay.displayName = DialogPrimitive.Overlay.displayName;

const ModalContent = React.forwardRef(({ className, children, ...props }, ref) => (
	<DialogPrimitive.Portal>
		<ModalOverlay />
		<DialogPrimitive.Content
			ref={ref}
			style={{ zIndex: 9999 }}
			className={cn(
				"fixed top-1/2 left-1/2 w-full max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-lg bg-background p-6 shadow-lg",
				className,
			)}
			{...props}
		>
			{children}
		</DialogPrimitive.Content>
	</DialogPrimitive.Portal>
));
ModalContent.displayName = DialogPrimitive.Content.displayName;

const ModalTitle = React.forwardRef(({ className, ...props }, ref) => (
	<DialogPrimitive.Title ref={ref} className={cn("text-lg font-bold", className)} {...props} />
));
ModalTitle.displayName = DialogPrimitive.Title.displayName;

const ModalDescription = React.forwardRef(({ className, ...props }, ref) => (
	<DialogPrimitive.Description
		ref={ref}
		className={cn("mt-2 text-sm text-gray-500", className)}
		{...props}
	/>
));
ModalDescription.displayName = DialogPrimitive.Description.displayName;

const ModalClose = React.forwardRef(({ className, ...props }, ref) => (
	<DialogPrimitive.Close
		ref={ref}
		className={cn("mt-4 px-4 py-2 bg-red-500 text-white rounded", className)}
		{...props}
	/>
));
ModalClose.displayName = DialogPrimitive.Close.displayName;

export { Modal, ModalTrigger, ModalContent, ModalTitle, ModalDescription, ModalClose };
