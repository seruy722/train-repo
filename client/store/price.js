// state
export const state = () => ({
    prices: [],
});

export const getters = {
    getPrices: state => state.prices,
};

export const mutations = {
    SET_PRICES (state, prices) {
        state.prices = prices;
    },
};

export const actions = {
    setPrices ({ commit }, prices) {
        commit('SET_PRICES', prices);
    },
};
