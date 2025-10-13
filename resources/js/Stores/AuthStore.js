import { create } from "zustand";

export const useAuthStore = create()(
	(set, get) => ({
		// State
		user: null,
		isAuthenticated: false,

		logout: () => {
			set({
				user: null,
				isAuthenticated: false,
			});
		},

		setUser: (user) => {
			set({ user, isAuthenticated: true });
		},

		updateUser: (updates) => {
			const currentUser = get().user;
			if (currentUser) {
				const updatedUser = { ...currentUser, ...updates };
				set({ user: updatedUser });
			}
		},
	}),
	{
		name: "auth-store",
		partialize: (state) => ({
			user: state.user,
			isAuthenticated: state.isAuthenticated,
		}),
	},
);
