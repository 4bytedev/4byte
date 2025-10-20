import js from "@eslint/js";
import reactPlugin from "eslint-plugin-react";
import prettier from "eslint-plugin-prettier";
import prettierConfig from "eslint-config-prettier";
import eslintPluginPrettierRecommended from "eslint-plugin-prettier/recommended";
import globals from "globals";

export default [
	js.configs.recommended,
	eslintPluginPrettierRecommended,
	{
		files: ["**/*.{js,jsx,ts,tsx}"],
		ignores: ["node_modules", "dist", "build"],
		languageOptions: {
			ecmaVersion: "latest",
			sourceType: "module",
			parserOptions: {
				ecmaFeatures: { jsx: true },
			},
			globals: {
				...globals.browser,
				...globals.node,
				route: "readonly",
			},
		},
		plugins: {
			react: reactPlugin,
			prettier,
		},
		rules: {
			...reactPlugin.configs.recommended.rules,
			...prettierConfig.rules,
			"react/react-in-jsx-scope": "off",
			"react/prop-types": "off",
			"prettier/prettier": [
				"error",
				{
					tabWidth: 4,
					useTabs: true,
					semi: true,
					trailingComma: "all",
					printWidth: 100,
					bracketSpacing: true,
					arrowParens: "always",
					endOfLine: "lf",
				},
			],
		},
		settings: {
			react: {
				version: "detect",
			},
		},
	},
];
