// STATE
export const state = () => ({
    search: '',
    currentTable: 'КАРГО',
    openedComponent: false,
});

// GETTERS
export const getters = {
    getSearch: state => state.search,
    getCurrentTable: state => state.currentTable,
    getOpenedComponent: state => state.openedComponent
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
    SET_SEARCH (state, string){
        state.search = string;
    },
    SET_TABLE (state, title){
        state.currentTable = title;
    },
    SET_OPENEDCOMPONENT (state, title){
        state.openedComponent = title;
    },
};

// ACTIONS
export const actions = {


};
