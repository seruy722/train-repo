// state
export const state = () => ({
    errors: {},
});

// getters
export const getters = {
    getErrors: state => state.errors,
};

// mutations
export const mutations = {
    SET_ERRORS (state, errors) {
        state.errors = errors;
    },

    DELETE_ERRORS (state) {
        state.errors = {};
    },
};

// actions
export const actions = {
    setErrors ({commit}, errors) {
        commit('SET_ERRORS', errors);
    },

    deleteErrors ({commit}) {
        commit('DELETE_ERRORS');
    },
    checkError ({getters}, field) {
        console.log('field', getters);
        // dispatch('deleteItemFromServer', data.id);
        // return state.errors.hasOwnProperty(field) ? state.errors[field] : [];
    },
};
