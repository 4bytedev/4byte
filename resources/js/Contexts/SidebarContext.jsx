import { createContext, useContext, useState } from "react";

const SidebarContext = createContext();

export function SidebarProvider({ children }) {
	const [isVisible, setIsVisible] = useState(false);
	const [isOpen, setIsOpen] = useState(false);

	const toggleSidebar = () => setIsOpen((prev) => !prev);

	return (
		<SidebarContext.Provider
			value={{ isVisible, setIsVisible, isOpen, setIsOpen, toggleSidebar }}
		>
			{children}
		</SidebarContext.Provider>
	);
}

export function useSidebar() {
	return useContext(SidebarContext);
}
