import moment from "moment";

/**
 * Хранилище для работы с таблицами cargos и debts
 */

// STATE
export const state = () => ({
    currentTable: 'КАРГО',
    currentClient: null,
    currentDate:  null,
    cargoList: [],
    debtsList: [],
    staticCargolist: [],
    staticDebtslist: [],
    clientsNames: [],
    countObject: {
        sum: 0,
        place: 0,
        kg: 0,
        sale: 0,
        commission: 0
    }
});

// GETTERS
export const getters = {
    cargoList: state => state.cargoList,
    debtsList: state => state.debtsList,
    clientsNames: state => state.clientsNames,
    countObject: state => state.countObject,
    getCurrentTable: state => state.currentTable,
    getCurrentClient: state => state.currentClient,
    getcurrentDate: state => state.currentDate,
};

// MUTATIONS
export const mutations = {
    // Установка списка карго
    SET_LIST (state, list) {
        state.cargoList = list;
        state.staticCargolist = list;
    },
    // Установка списка долгов
    SET_DEBTS_LIST (state, list) {
        state.debtsList = list;
        state.staticDebtslist = list;
    },
    // Добавление записи в таблицу
    ADD_ITEM (state, item) {
        state.cargoList.unshift(item);
    },
    // Обновление записи в таблице
    UPDATE_ITEM (state, data) {
        const elementIndex = data.index;
        const item = data.item;

        state.list.splice(elementIndex, 1, item);
    },
    SET_CURRENTDATE (state, date) {
        state.currentDate = date;
    },
    // Удаление записи с таблицы
    DELETE_ITEM (state, index) {
        state.list.splice(index, 1);
    },
    // Общая сумма
    CALCULATE_SUM (state) {
        state.countObject.sum = _.sumBy(state.cargoList, (item) => {
            return item.sum;
        });
    },
    // Количество мест
    CALC_PLACES (state) {
        state.countObject.place = _.sumBy(state.cargoList, (item) => {
            return item.place;
        });
    },
    // Количество кг
    CALC_KG (state) {
        state.countObject.kg = _.sumBy(state.cargoList, (item) => {
            return item.kg;
        });
    },
    // Скидки по карго
    CALC_SALE (state) {
        state.countObject.sale = _.sumBy(state.cargoList, (item) => {
            return item.sale;
        });
    },
    // Комиссия по долгам
    CALC_COMISSION (state) {
        state.countObject.commission = _.sumBy(state.debtsList, (item) => {
            return item.commission;
        });
    },
    // Установка таблицы
    SET_TABLE (state, title) {
        state.currentTable = title;
    },
    // Установка клиента
    SET_CLIENT (state, client) {
        state.currentClient = client;
    },
    SET_CLIENTSNAMES (state, arrayClients) {
        state.clientsNames = arrayClients;
    },
    // Изменение списка в зависимости от выьора таблицы, клиента, даты
    CHANGE_CARGOLIST (state) {
        const client = state.currentClient || null;
        const date = state.currentDate;
        const table = state.currentTable;
        console.log('client', client);
        console.log('date', date);


        switch (table) {
            case 'КАРГО':
                if (client && date) {
                    // state.cargoList = _.filter(state.staticCargolist, (item) => {
                    //     return item.client === client;
                    // });
                    state.cargoList = _.reduce(state.staticCargolist, (result, item) => {
                        if (item.client === client && item.created_at === date) {
                            result.push(item);
                        }
                        return result;
                    }, []);
                    return;
                }
                state.cargoList = state.staticCargolist;
                break;

            case 'ДОЛГИ':
                if (client) {
                    state.debtsList = _.filter(state.staticDebtslist, (item) => {
                        return item.client === client;
                    });

                    return;
                }
                state.debtsList = state.staticDebtslist;
                break;
        }
    }
};

// ACTIONS
export const actions = {
    // Запуск подщетов
    calcData ({commit, state}) {
        if (state.currentTable === 'КАРГО') {
            commit('CALCULATE_SUM');
            commit('CALC_PLACES');
            commit('CALC_KG');
            commit('CALC_SALE');
            commit('CALC_COMISSION');
        } else {
            commit('CALCULATE_SUM');
            commit('CALC_COMISSION');
        }
    }
};
