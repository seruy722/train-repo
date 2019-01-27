// Проверка и вывод ошибок с сервера
export default {
    data: () => ({
        errors: {},
    }),
    methods: {
        checkError (field) {
            return _.has(this.errors, field) ? this.errors[field] : [];
        },
        changeErrors (value) {
            this.errors = value;
        },
    },
};
