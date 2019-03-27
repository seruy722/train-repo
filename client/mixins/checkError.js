// Проверка и вывод ошибок с сервера
export default {
    data: () => ({
        errors: {},
    }),
    methods: {
        checkError (field) {
            if (_.isArray(field)) {
                const includesElem = _.some(field, elem => _.has(this.errors, elem));
                if (includesElem) {
                    return this.errors[_.head(field)];
                }
            }
            return _.has(this.errors, field) ? this.errors[field] : [];
        },
        changeErrors (value) {
            this.errors = value;
        },
    },
};
