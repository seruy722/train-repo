// STATE
export const state = () => ({
    cargoList: [],
    clientsNames: []
});

// GETTERS
export const getters = {
    cargoList: state => state.cargoList,
    clientsNames: state => state.clientsNames
};

// MUTATIONS
export const mutations = {
    SET_LIST (state, list) {
        state.cargoList = list;
    },

    SET_CLIENTSNAMES (state, list){
        state.clientsNames = list;
    },

    ADD_ITEM (state, item) {
        state.cargoList.unshift(item);
    },

    UPDATE_ITEM (state, data) {
        const elementIndex = data.index;
        const item = data.item;

        state.list.splice(elementIndex, 1, item);
    },

    DELETE_ITEM (state, index) {
        state.list.splice(index, 1);
    }
};

// ACTIONS
export const actions = {};
