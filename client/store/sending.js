export const state = () => ({
    sendingData: [],
    message: 'Товар приехал. Сумма:',
});

export const getters = {
    sendingData: state => state.sendingData,
    message: state => state.message,
};

export const mutations = {
    SET_SENDING_DATA (state, data) {
        state.sendingData = data;
    },
    SET_MESSAGE (state, data) {
        state.message = data;
    },
};

export const actions = {
    setSendingData ({ commit }, data) {
        commit('SET_SENDING_DATA', data);
    },
    setMessage ({ commit }, data) {
        commit('SET_MESSAGE', data);
    },
};
