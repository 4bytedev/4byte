import { Card, CardContent, CardHeader, CardTitle } from "@/Components/Ui/Card";
import { Label } from "@/Components/Ui/Form/Label";
import { Switch } from "@/Components/Ui/Form/Switch";
import { TabsContent } from "@/Components/Ui/Tabs";
import { useTheme } from "@/Contexts/ThemeContext";
import { Trans, useTranslation } from "react-i18next";

export function AppearanceSettings() {
	const { t } = useTranslation();
	const { theme, toggleTheme } = useTheme();

	return (
		<TabsContent value="appearance" className="space-y-6">
			<Card>
				<CardHeader>
					<CardTitle>{t("Theme")}</CardTitle>
				</CardHeader>
				<CardContent>
					<div className="flex items-center justify-between">
						<div>
							<Label>{t("Dark Mode")}</Label>
							<p className="text-sm text-muted-foreground">
								<Trans
									i18nKey="using_theme"
									values={{ theme: t(theme) }}
									components={{ strong: <strong /> }}
								/>
							</p>
						</div>
						<Switch checked={theme === "dark"} onCheckedChange={toggleTheme} />
					</div>
				</CardContent>
			</Card>
		</TabsContent>
	);
}
