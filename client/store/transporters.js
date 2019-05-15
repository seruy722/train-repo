export const state = () => ({
    transporters: [],
});

export const getters = {
    transporters: state => state.transporters,
};

export const mutations = {
    SET_TRANSPORTERS (state, data) {
        state.transporters = data;
    },
};

export const actions = {
    setTransporters ({ commit }, data) {
        commit('SET_TRANSPORTERS', data);
    },
};
