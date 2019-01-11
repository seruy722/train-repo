/**
 * Хранилище для работы с таблицами cargos и debts
 */
import { numberFormat, DateClass } from '~/utils';

const dateClass = new DateClass();

// STATE
export const state = () => ({
    currentTable: 'КАРГО',
    currentClient: 'Все',
    currentDate: {
        title: 'Все даты',
        day: null,
        startDate: null,
        endDate: null,
        year: null,
        month: null,
    },
    sensor: null,
    progressbarTable: false,
    cargoList: [],
    debtsList: [],
    staticCargolist: [],
    staticDebtslist: [],
    mainCalcList: [],
    clientsNames: [],
    countObject: {
        sum: 0,
        place: 0,
        kg: 0,
        sale: 0,
        commission: 0,
    },
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
    getProgressbarTable: state => state.progressbarTable,
    getSensor: state => state.sensor,
};

// MUTATIONS
export const mutations = {
    // SET_LOCAL_LIST (state) {
    //     if (state.currentTable === 'КАРГО') {
    //         state.mainCalcList = state.cargoList;
    //     } else {
    //         state.mainCalcList = state.debtsList;
    //     }
    // },
    // Установка списка карго
    SET_LIST (state, list) {
        state.cargoList = list;
        state.staticCargolist = list;
    },
    SET_SENSOR (state, value) {
        state.sensor = value;
    },
    // Установка списка долгов
    SET_DEBTS_LIST (state, list) {
        state.debtsList = list;
        state.staticDebtslist = list;
    },
    // Установка прогресс бара в таблице
    SET_PROGRESSBAR_TABLE (state, value) {
        state.progressbarTable = value;
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
        console.log('DATE', date);
        _.assign(state.currentDate, date);
    },
    // Удаление записи с таблицы
    DELETE_ITEM (state, index) {
        state.list.splice(index, 1);
    },
    // Общая сумма
    CALC_SUM (state) {
        let list = null;

        if (state.currentTable === 'КАРГО') {
            list = state.cargoList;
        } else {
            list = state.debtsList;
        }

        state.countObject.sum = numberFormat(_.sumBy(list, (item) => {
            return item.sum;
        }));
    },
    // Количество мест
    CALC_PLACES (state) {
        state.countObject.place = numberFormat(_.sumBy(state.cargoList, (item) => {
            return item.place;
        }));
    },
    // Количество кг
    CALC_KG (state) {
        state.countObject.kg = numberFormat(_.sumBy(state.cargoList, (item) => {
            return item.kg;
        }));
    },
    // Скидки по карго
    CALC_SALE (state) {
        state.countObject.sale = numberFormat(_.sumBy(state.cargoList, (item) => {
            return item.sale;
        }));
    },
    // Комиссия по долгам
    CALC_COMISSION (state) {
        state.countObject.commission = numberFormat(_.sumBy(state.debtsList, (item) => {
            return item.commission;
        }));
    },
    // Установка таблицы
    SET_TABLE (state, table) {
        state.currentTable = table;
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
        const client = state.currentClient;
        const date = state.currentDate;
        const table = state.currentTable;
        console.log('client', client);
        console.log('date', date);

        switch (table) {
            case 'КАРГО':
                switch (date.title) {
                    case 'Все даты':
                        state.sensor = date.title;
                        if (client !== 'Все') {
                            state.cargoList = _.filter(state.staticCargolist, item => item.client === client);
                            return;
                        }

                        break;
                    case 'Выбрать день':
                    case 'Сегодня':
                        if (!date.day) {
                            state.sensor = 'Сегодня';
                            const today = new Date().toISOString().substr(0, 10).split('-').reverse().join('-');
                            state.cargoList = _.filter(state.staticCargolist, item => item.created_at === today);

                            return;
                        }

                        state.sensor = date.day;
                        if (client !== 'Все') {
                            state.cargoList = _.reduce(state.staticCargolist, (result, item) => {
                                if (item.created_at === date.day && item.client === client) {
                                    result.push(item);
                                }
                                return result;
                            }, []);
                            return;
                        } else {
                            state.cargoList = _.filter(state.staticCargolist, item => item.created_at === date.day);
                            return;
                        }

                    case 'Выбрать неделю':
                    case 'Выбрать месяц':
                    case 'Выбрать год':
                    case 'Текущая неделя':
                    case 'Текущий год':
                    case 'Текущий месяц':
                    case 'Выбрать период':
                        console.log('DSD', date);
                        if (date.year) {
                            console.log('date.year', date.year);
                            const yearDates = dateClass.startEndDateOfYear(date.year);
                            date.startDate = yearDates.startDate;
                            date.endDate = yearDates.endDate;
                        }

                        if (date.month) {
                            console.log('date.year', date.month);
                            const monthDates = dateClass.startEndDateOfMonth(date.month);
                            date.startDate = monthDates.startDate;
                            date.endDate = monthDates.endDate;
                        }

                        if (date.startDate && date.endDate) {
                            state.sensor = `${date.startDate} по ${date.endDate}`;
                        } else if (date.startDate && !date.endDate) {
                            state.sensor = `С ${date.startDate}`;
                        } else if (!date.startDate && date.endDate) {
                            state.sensor = `По ${date.endDate}`;
                        }

                        let start = null;
                        let end = null;

                        if (date.endDate) {
                            const endDate = date.endDate.split('-').reverse().join('/');
                            end = new Date(endDate);
                        }

                        if (date.startDate) {
                            const startDate = date.startDate.split('-').reverse().join('/');
                            start = new Date(startDate);
                        }

                        if (!date.startDate) {
                            if (client !== 'Все') {
                                state.cargoList = _.reduce(state.staticCargolist, (result, item) => {
                                    const currentDate = item.created_at.split('-').reverse().join('/');
                                    const itemDate = new Date(currentDate);

                                    if (itemDate <= end && item.client === client) {
                                        result.push(item);
                                    }
                                    return result;
                                }, []);

                                return;
                            } else {
                                state.cargoList = _.reduce(state.staticCargolist, (result, item) => {
                                    const currentDate = item.created_at.split('-').reverse().join('/');
                                    const itemDate = new Date(currentDate);

                                    if (itemDate <= end) {
                                        result.push(item);
                                    }
                                    return result;
                                }, []);

                                return;
                            }
                        }

                        if (!date.endDate) {
                            if (client !== 'Все') {
                                state.cargoList = _.reduce(state.staticCargolist, (result, item) => {
                                    const currentDate = item.created_at.split('-').reverse().join('/');
                                    const itemDate = new Date(currentDate);

                                    if (itemDate >= start && item.client === client) {
                                        result.push(item);
                                    }
                                    return result;
                                }, []);
                                return;
                            } else {
                                state.cargoList = _.reduce(state.staticCargolist, (result, item) => {
                                    const currentDate = item.created_at.split('-').reverse().join('/');
                                    const itemDate = new Date(currentDate);

                                    if (itemDate >= start) {
                                        result.push(item);
                                    }
                                    return result;
                                }, []);
                                return;
                            }
                        }


                        if (client !== 'Все') {
                            state.cargoList = _.reduce(state.staticCargolist, (result, item) => {
                                const currentDate = item.created_at.split('-').reverse().join('/');
                                const itemDate = new Date(currentDate);

                                if (itemDate >= start && itemDate <= end && item.client === client) {
                                    result.push(item);
                                }
                                return result;
                            }, []);

                            return;
                        } else {
                            state.cargoList = _.reduce(state.staticCargolist, (result, item) => {
                                const currentDate = item.created_at.split('-').reverse().join('/');
                                const itemDate = new Date(currentDate);

                                if (itemDate >= start && itemDate <= end) {
                                    result.push(item);
                                }
                                return result;
                            }, []);

                            return;
                        }
                }

                state.cargoList = state.staticCargolist;
                break;

            case 'ДОЛГИ':

                state.debtsList = state.staticDebtslist;
                break;
        }
    },
};

// ACTIONS
export const actions = {
    // Запуск подщетов
    calcData ({ dispatch, state }) {
        if (state.currentTable === 'КАРГО') {
            dispatch('calcSum');
            dispatch('calcPlaces');
            dispatch('calcKg');
            dispatch('calcSale');
            dispatch('calcComission');
        } else {
            dispatch('calcSum');
            dispatch('calcComission');
        }
    },
    calcSum ({ commit }) {
        commit('CALC_SUM');
    },
    calcKg ({ commit }) {
        commit('CALC_KG');
    },
    calcPlaces ({ commit }) {
        commit('CALC_PLACES');
    },
    calcSale ({ commit }) {
        commit('CALC_SALE');
    },
    calcComission ({ commit }) {
        commit('CALC_COMISSION');
    },
    setList ({ commit }, list) {
        commit('SET_LIST', list);
    },
    changeList ({ commit, dispatch }) {
        commit('SET_PROGRESSBAR_TABLE', true);
        commit('CHANGE_CARGOLIST');
        dispatch('calcData');
        commit('SET_PROGRESSBAR_TABLE', false);
    },
    setClientsNames ({ commit }, clientNames) {
        commit('SET_CLIENTSNAMES', clientNames);
    },
};
