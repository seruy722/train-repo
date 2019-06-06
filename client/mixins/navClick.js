// Переход по меню
export default {
    methods: {
        navClick (item) {
            const { path } = item;
            console.log('PATH', path);
            if (path === '/logout') {
                this.logout();
            } else if (path) {
                this.$router.push(path);
            }
        },
    },
};
