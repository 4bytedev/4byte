import React, { useState, useEffect } from "react";
import { Button } from "@/Components/Ui/Button";
import { Cookie } from "lucide-react";
import { useSiteStore } from "@/Stores/SiteStore";
import { Link } from "@inertiajs/react";

const CookieConsent = () => {
	const [showBanner, setShowBanner] = useState(false);
	const siteStore = useSiteStore();
	const COOKIE_NAME = "cookie_consent";
	const COOKIE_VALUE = 1;
	const COOKIE_LIFETIME = 365 * 20;
	const COOKIE_DOMAIN = window.location.hostname;

	useEffect(() => {
		if (!cookieExists(COOKIE_NAME)) {
			setShowBanner(true);
		}
	}, []);

	function cookieExists(name) {
		return document.cookie.split("; ").includes(name + "=" + COOKIE_VALUE);
	}

	function setCookie(name, value, expirationInDays) {
		const date = new Date();
		date.setTime(date.getTime() + expirationInDays * 24 * 60 * 60 * 1000);

		document.cookie =
			name +
			"=" +
			value +
			";expires=" +
			date.toUTCString() +
			";domain=" +
			COOKIE_DOMAIN +
			";path=/;secure;samesite=lax";
	}

	function consentWithCookies() {
		setCookie(COOKIE_NAME, COOKIE_VALUE, COOKIE_LIFETIME);
		setShowBanner(false);
	}

	if (!showBanner) return null;

	return (
		<div className="fixed right-5 bottom-5 z-50 animate-fade-in-up">
			<div className="bg-muted rounded-xl shadow-2xl p-6 max-w-2xl mx-auto border border-gray-200">
				<div className="flex items-start mb-4">
					<Cookie className="w-10 h-10"></Cookie>
					<div className="ml-4">
						<h3 className="text-lg font-semibold">We use cookies</h3>
						<p className="mt-1 text-sm">
							Your experience on this site will be improved by allowing cookies.
						</p>
					</div>
				</div>
				<div className="flex flex-wrap gap-3 mt-4 justify-center">
					<Button onClick={consentWithCookies} color="yellow">
						Accept All
					</Button>
					{siteStore.pages.terms && (
						<Button color="gray" asChild>
							<Link href={route("page.view", { slug: siteStore.pages.terms })}>
								Privacy Policy
							</Link>
						</Button>
					)}
				</div>
			</div>
		</div>
	);
};

export default CookieConsent;
