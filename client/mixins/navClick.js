// Переход по меню
export default {
    methods: {
        navClick (item) {
            if (item.path === '/logout') {
                this.logout();
            } else {
                this.$router.push(item.path);
            }
        },
    }
}
