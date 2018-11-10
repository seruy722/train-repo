// Выход из панели управления
export default {
    methods: {
        async logout () {
            // Log out the user.
            await this.$store.dispatch('auth/logout');

            // Redirect to login.
            this.$router.push('/login');
        }
    }
}
