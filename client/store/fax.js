import Cookies from 'js-cookie';

export const state = () => ({
    faxID: null,
    faxName: null,
    fax: null,
});

export const getters = {
    getFaxID: state => state.faxID || Cookies.get('faxID'),
    getFaxName: state => state.faxName || Cookies.get('faxName'),
    getFax: state => state.fax,
};

export const mutations = {
    SET_FAX_ID (state, faxID) {
        state.faxID = faxID;
    },

    SET_FAX_NAME (state, faxName) {
        state.faxName = faxName;
    },

    SET_FAX (state, fax) {
        state.fax = fax;
    },
};

export const actions = {
    setFaxID ({ commit }, faxID) {
        commit('SET_FAX_ID', faxID);
        if (faxID) {
            Cookies.set('faxID', faxID, { expires: 1 });
        }
    },

    setFaxName ({ commit }, faxName) {
        commit('SET_FAX_NAME', faxName);
        if (faxName) {
            Cookies.set('faxName', faxName, { expires: 1 });
        }
    },

    setFax ({ commit }, fax) {
        commit('SET_FAX', fax);
    },

};
