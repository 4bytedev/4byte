import React, { useEffect } from "react";
import { Toaster } from "sonner";
import { ThemeProvider } from "@/Contexts/ThemeContext";
import { Navbar } from "@/Components/Layout/Navbar";
import { usePage, router } from "@inertiajs/react";
import { useAuthStore } from "@/Stores/AuthStore";
import { useSiteStore } from "@/Stores/SiteStore";
import LoginModal from "@/Components/Modals/Auth/LoginModal";
import RegisterModal from "@/Components/Modals/Auth/RegisterModal";
import ForgotPasswordModal from "@/Components/Modals/Auth/ForgotPasswordModal";
import ApiService from "@/Services/ApiService";
import CookieConsent from "@/Components/Common/CookieConsent";
import { I18nProvider } from "@/Contexts/I18nContext";
import { ToastContext } from "@/Contexts/ToastContext";
import { SidebarProvider } from "@/Contexts/SidebarContext";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { useModalStore } from "@/Stores/ModalStore";

export default function Layout({ children }) {
	const { site, account, csrf_token } = usePage().props;
	const { url } = usePage();

	const authStore = useAuthStore();
	const siteStore = useSiteStore();
	const modalStore = useModalStore();

	const queryClient = new QueryClient();

	useEffect(() => {
		ApiService.setToken(csrf_token);
	}, [csrf_token]);

	useEffect(() => {
		if (account) {
			authStore.setUser(account);
			if (
				!account.verified &&
				url !== "/user/me/verification" &&
				siteStore.settings.verification
			) {
				router.visit(route("user.verification.view"), {
					method: "get",
				});
			}
		}
	}, [account]);

	useEffect(() => {
		if (site) {
			siteStore.setSite(site);
		}
	}, [site]);

	useEffect(() => {
		router.on("navigate", () => {
			modalStore.closeAll();
		});
	}, []);

	return (
		<I18nProvider>
			<ThemeProvider>
				<QueryClientProvider client={queryClient}>
					<SidebarProvider>
						<div className="min-h-screen bg-background">
							<Navbar />
							{children}
							<Toaster />
						</div>
						{siteStore.settings.login && !authStore.isAuthenticated && (
							<>
								<LoginModal />
								<ForgotPasswordModal />
							</>
						)}
						{siteStore.settings.register && !authStore.isAuthenticated && (
							<RegisterModal />
						)}
						<CookieConsent />
						<ToastContext />
					</SidebarProvider>
				</QueryClientProvider>
			</ThemeProvider>
		</I18nProvider>
	);
}
