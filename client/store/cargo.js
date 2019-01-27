/**
 * Хранилище для работы с таблицами cargos и debts
 */
import { numberFormat, numberUnformat } from '~/utils';
import { startEndDateOfYear, startEndDateOfMonth, today } from '~/utils/dates';

// STATE
export const state = () => ({
    currentTable: 'КАРГО',
    currentClient: null,
    // Чтобы при каждом добавлении записи не выберать дату
    dateForAddEntry: today(),
    currentDate: {
        title: 'Все даты',
        day: null,
        id: 1,
        startDate: null,
        endDate: null,
        year: null,
        month: null,
    },
    sensor: null,
    progressbarTable: false,
    list: [],
    allDataList: [],
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
        commission: 0,
    },
});

// GETTERS
export const getters = {
    clientsNames: state => state.clientsNames,
    countObject: state => state.countObject,
    getCurrentTable: state => state.currentTable,
    getDateForAddEntry: state => state.dateForAddEntry,
    getCurrentClient: state => state.currentClient,
    getcurrentDate: state => state.currentDate,
    getProgressbarTable: state => state.progressbarTable,
    getSensor: state => state.sensor,
    getList: state => state.list,
};

// MUTATIONS
export const mutations = {
    // Установка списка карго
    SET_LIST (state, list) {
        state.list = list;
        state.allDataList = list;
        state.cargoList = list;
        state.staticCargolist = list;
    },
    SET_SENSOR (state, value) {
        state.sensor = value;
    },
    // Установка списка долгов
    SET_DEBTS_LIST (state, list) {
        state.list = list;
        state.allDataList = list;
        state.debtsList = list;
        state.staticDebtslist = list;
    },
    // Установка прогресс бара в таблице
    SET_PROGRESSBAR_TABLE (state, value) {
        state.progressbarTable = value;
    },
    // Добавление записи в таблицу
    ADD_ITEM (state, item) {
        state.list.unshift(item);
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
    // Изменение даты при добавлении записей
    SET_DATEFORADDENTRY (state, date) {
        state.dateForAddEntry = date;
    },
    // Удаление записи с таблицы
    DELETE_ITEM (state, index) {
        state.list.splice(index, 1);
    },
    // Общая сумма
    CALC_SUM (state) {
        state.countObject.sum = numberFormat(_.sumBy(state.list, item => numberUnformat(item.sum)));
    },
    // Количество мест
    CALC_PLACES (state) {
        state.countObject.place = numberFormat(_.sumBy(state.list, item => numberUnformat(item.place)));
    },
    // Количество кг
    CALC_KG (state) {
        state.countObject.kg = numberFormat(_.sumBy(state.list, item => numberUnformat(item.kg)));
    },
    // Скидки по карго
    CALC_SALE (state) {
        state.countObject.sale = numberFormat(_.sumBy(state.list, item => numberUnformat(item.sale)));
    },
    // Комиссия по долгам
    CALC_COMISSION (state) {
        state.countObject.commission = numberFormat(_.sumBy(state.list, item => numberUnformat(item.commission)));
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
    SWITCH_LISTS (state) {
        if (state.currentTable === 'КАРГО') {
            state.list = state.cargoList;
            state.allDataList = state.staticCargolist;
        } else {
            state.list = state.debtsList;
            state.allDataList = state.staticDebtslist;
        }
    },
    // Изменение списка в зависимости от выьора таблицы, клиента, даты
    CHANGE_CARGOLIST (state) {
        const clientID = _.toNumber(state.currentClient.id);
        const date = state.currentDate;
        // console.log('clientID', clientID);
        // console.log('date', date);

        switch (date.id) {
            // Все даты
            case 1:
                if (clientID) {
                    state.list = _.filter(state.allDataList, item => _.toNumber(item.client_id) === clientID);
                }
                break;
            case 7: // Выбрать день
            case 2: // Сегодня
                date.title = date.day;
                state.sensor = date;
                if (clientID) {
                    state.list = _.reduce(state.allDataList, (result, item) => {
                        if (item.created_at === date.day && _.toNumber(item.client_id) === clientID) {
                            result.push(item);
                        }
                        return result;
                    }, []);
                } else {
                    state.list = _.filter(state.allDataList, item => item.created_at === date.day);
                }
                break;

            case 10: // Выбрать неделю
            case 9: // Выбрать месяц
            case 8: // Выбрать год
            case 5: // Текущая неделя
            case 4: // Текущий год
            case 3: // Текущий месяц
            case 6: // Выбрать период
                if (date.year) {
                    const yearDates = startEndDateOfYear(date.year);
                    _.assign(date, yearDates);
                }

                if (date.month) {
                    const monthDates = startEndDateOfMonth(date.month);
                    _.assign(date, monthDates);
                }

                if (date.startDate && date.endDate) {
                    date.title = `${date.startDate} по ${date.endDate}`;
                    state.sensor = date;
                } else if (date.startDate && !date.endDate) {
                    date.title = `С ${date.startDate}`;
                    state.sensor = date;
                } else if (!date.startDate && date.endDate) {
                    date.title = `По ${date.endDate}`;
                    state.sensor = date;
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
                    if (clientID) {
                        state.list = _.reduce(state.allDataList, (result, item) => {
                            const currentDate = item.created_at.split('-').reverse().join('/');
                            const itemDate = new Date(currentDate);

                            if (itemDate <= end && _.toNumber(item.client_id) === clientID) {
                                result.push(item);
                            }
                            return result;
                        }, []);

                        return;
                    } else {
                        state.list = _.reduce(state.allDataList, (result, item) => {
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
                    if (clientID) {
                        state.list = _.reduce(state.allDataList, (result, item) => {
                            const currentDate = item.created_at.split('-').reverse().join('/');
                            const itemDate = new Date(currentDate);

                            if (itemDate >= start && _.toNumber(item.client_id) === clientID) {
                                result.push(item);
                            }
                            return result;
                        }, []);
                        return;
                    } else {
                        state.list = _.reduce(state.allDataList, (result, item) => {
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


                if (clientID) {
                    state.list = _.reduce(state.allDataList, (result, item) => {
                        const currentDate = item.created_at.split('-').reverse().join('/');
                        const itemDate = new Date(currentDate);

                        if (itemDate >= start && itemDate <= end && _.toNumber(item.client_id) === clientID) {
                            result.push(item);
                        }
                        return result;
                    }, []);

                    return;
                } else {
                    state.list = _.reduce(state.allDataList, (result, item) => {
                        const currentDate = item.created_at.split('-').reverse().join('/');
                        const itemDate = new Date(currentDate);

                        if (itemDate >= start && itemDate <= end) {
                            result.push(item);
                        }
                        return result;
                    }, []);

                    return;
                }
            default:
                state.list = state.allDataList;
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
        commit('SWITCH_LISTS');
        commit('CHANGE_CARGOLIST');
        dispatch('calcData');
        commit('SET_PROGRESSBAR_TABLE', false);
    },
    setClientsNames ({ commit }, clientNames) {
        commit('SET_CLIENTSNAMES', clientNames);
    },
};
