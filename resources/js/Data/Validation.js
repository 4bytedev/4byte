export default {
	password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$@!%&.*?])[A-Za-z\d#$@!%&.*?]{8,}$/,
	email: /\S+@\S+\.\S+/,
	username: /^[a-zA-Z0-9]+/,
};
