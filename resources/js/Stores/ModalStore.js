import { create } from "zustand";

export const useModalStore = create()(
	(set, get) => ({
		login: false,
		register: false,
		forgotPassword: false,
		navigation: false,
		passwordConfirmation: false,

		open: (modalToOpen) => () => {
			const allModalsClosed = Object.keys(get()).reduce((acc, key) => {
				if (typeof get()[key] === "boolean") {
					acc[key] = false;
				}
				return acc;
			}, {});

			set({ ...allModalsClosed, [modalToOpen]: true });
		},

		close: (modal) => () => {
			set({ [modal]: false });
		},

		closeAll: () => {
			set({
				login: false,
				register: false,
				forgotPassword: false,
				navigation: false,
				passwordConfirmation: false,
			});
		},

		toggle: (modal) => () => {
			const currentStatus = get()[modal];
			set({ [modal]: !currentStatus });
		},
	}),
	{
		name: "modal-store",
		partialize: (state) => state,
	},
);
