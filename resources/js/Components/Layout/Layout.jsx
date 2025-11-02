import React, { useEffect } from "react";
import { Toaster } from "sonner";
import { ThemeProvider } from "@/Contexts/ThemeContext";
import { Navbar } from "@/Components/Layout/Navbar";
import { usePage, router } from "@inertiajs/react";
import { useAuthStore } from "@/Stores/AuthStore";
import { useSiteStore } from "@/Stores/SiteStore";
import LoginModal from "@/Modals/Auth/LoginModal";
import RegisterModal from "@/Modals/Auth/RegisterModal";
import ForgotPasswordModal from "@/Modals/Auth/ForgotPasswordModal";
import ApiService from "@/Services/ApiService";
import CookieConsent from "@/Components/Ui/CookieConsent";
import { I18nProvider } from "@/Contexts/I18nContext";
import { ToastContext } from "@/Contexts/ToastContext";
import { SidebarProvider } from "@/Contexts/SidebarContext";

export default function Layout({ children }) {
	const { site, account, csrf_token } = usePage().props;

	const { url } = usePage();
	const authStore = useAuthStore();
	const siteStore = useSiteStore();

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

	return (
		<I18nProvider>
			<ThemeProvider>
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
					{siteStore.settings.register && !authStore.isAuthenticated && <RegisterModal />}
					<CookieConsent />
					<ToastContext />
				</SidebarProvider>
			</ThemeProvider>
		</I18nProvider>
	);
}
