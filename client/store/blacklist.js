import axios from 'axios';
// import Cookies from 'js-cookie';

// STATE
export const state = () => ({
    list: []
});

// GETTERS
export const getters = {
    getlist: state => state.list
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
    }
};

// ACTIONS
export const actions = {};
