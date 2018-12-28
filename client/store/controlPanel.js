import moment from 'moment';

// STATE
export const state = () => ({
    search: '',
    // currentTable: 'КАРГО',
    openedComponent: false,
    currentClient: null,
    // currentDate:  moment().format('DD-MM-YYYY'),
});

// GETTERS
export const getters = {
    getSearch: state => state.search,
    // getCurrentTable: state => state.currentTable,
    // getCurrentClient: state => state.currentClient,
    getOpenedComponent: state => state.openedComponent,
    // getcurrentDate: state => state.currentDate,

};

// MUTATIONS
export const mutations = {
    SET_LIST (state, list) {
        state.list = list;
    },

    ADD_ITEM (state, item) {
        state.list.unshift(item);
    },

    UPDATE_ITEM (state, data) {
        const elementIndex = data.index;
        const item = data.item;

        state.list.splice(elementIndex, 1, item);
    },

    DELETE_ITEM (state, index) {
        state.list.splice(index, 1);
    },
    SET_SEARCH (state, string) {
        state.search = string;
    },
    // SET_TABLE (state, title) {
    //     state.currentTable = title;
    // },
    // SET_CLIENT (state, client) {
    //     state.currentClient = client;
    // },
    SET_OPENEDCOMPONENT (state, title) {
        state.openedComponent = title;
    },
    // SET_CURRENTDATE (state, date) {
    //     state.currentDate = date;
    // }
};

// ACTIONS
export const actions = {};
