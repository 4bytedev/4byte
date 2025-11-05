import { User, Palette, Shield, Settings } from "lucide-react";
import { Tabs, TabsList, TabsTrigger } from "@/Components/Ui/Tabs";
import { useTranslation } from "react-i18next";
import { AccountSettings } from "./SettingsTabs/AccountSettings";
import { ProfileSettings } from "./SettingsTabs/ProfileSettings";
import { AppearanceSettings } from "./SettingsTabs/AppearanceSettings";
import { SecuritySettings } from "./SettingsTabs/SecuritySettings";

export default function SettingsPage({ account, profile, sessions }) {
	const { t } = useTranslation();

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-4xl mx-auto">
				<div className="mb-8">
					<h1 className="text-3xl font-bold mb-2">{t("Settings")}</h1>
					<p className="text-muted-foreground">
						{t("Manage your account settings and preferences")}
					</p>
				</div>

				<Tabs defaultValue="account" className="w-full">
					<TabsList className="grid w-full grid-cols-4">
						<TabsTrigger value="account">
							<Settings className="h-4 w-4 mr-2" />
							{t("Account")}
						</TabsTrigger>
						<TabsTrigger value="profile">
							<User className="h-4 w-4 mr-2" />
							{t("Profile")}
						</TabsTrigger>
						<TabsTrigger value="appearance">
							<Palette className="h-4 w-4 mr-2" />
							{t("Appearance")}
						</TabsTrigger>
						<TabsTrigger value="security">
							<Shield className="h-4 w-4 mr-2" />
							{t("Security")}
						</TabsTrigger>
					</TabsList>

					<AccountSettings account={account} />

					<ProfileSettings profile={profile} />

					<AppearanceSettings />

					<SecuritySettings sessions={sessions} />
				</Tabs>
			</div>
		</div>
	);
}
