import axios from 'axios';

export const state = () => ({
    groupedData: {},
    tableCategoriesData: [],
    clients: [],
    categories: [],
    faxData: [],
    removedEtries: [],
    faxesNames: [],
    faxes: [],
    searchValue: '',
});

export const getters = {
    getGroupedData: state => state.groupedData,
    getTableCategoriesData: state => state.tableCategoriesData,
    getClients: state => state.clients,
    getCategories: state => state.categories,
    getFaxData: state => state.faxData,
    getFaxesNames: state => state.faxesNames,
    getFaxes: state => state.faxes,
    getSearchValue: state => state.searchValue,
};

export const mutations = {
    SET_SEARCH_VALUE (state, value) {
        state.searchValue = value;
    },

    SET_GROUPED_DATA (state, groupedData) {
        state.groupedData = groupedData;
    },

    SET_TABLE_CATEGORIES_DATA (state, tableCategoriesData) {
        state.tableCategoriesData = tableCategoriesData;
    },

    SET_CLIENTS_NAMES (state, clients) {
        state.clients = clients;
    },

    SET_CATEGORIES (state, categories) {
        state.categories = categories;
    },

    SET_FAX_DATA (state, faxData) {
        state.faxData = faxData;
    },

    DESTROY_ENTRIES_FROM_GROUPED_DATA (state, objData) {
        if (state.groupedData[objData.clientID]) {
            const removedEtries = _.remove(state.groupedData[objData.clientID], item => _.includes(objData.entriesID, item.id));
            state.removedEtries.push(...removedEtries);
            console.log('removedEtry', removedEtries);
        }
    },

    SET_FAXES_NAMES (state, faxesNames) {
        state.faxesNames = faxesNames;
    },

    SET_FAXES (state, allFaxes) {
        state.faxes = allFaxes;
    },

    ADD_FAX (state, fax) {
        state.faxes.unshift(fax);
    },

    DESTROY_FAXES (state, ids) {
        _.forEach(ids, (value) => {
            const index = _.findIndex(state.faxes, { id: value });
            state.faxes.splice(index, 1);
        });
    },
};

export const actions = {
    setGroupedData ({ commit, dispatch }, data) {
        commit('SET_GROUPED_DATA', data);
        dispatch('dataAggregation', data);
    },

    async dataAggregation ({ dispatch }, data) {
        console.log('AGGR');
        const result = [];

        const arrayForGroupItem = (data) => {
            const obj = _.get(data, 'obj') || _.get(data, 'findedObj');

            if (obj.brand) {
                return _.filter(data.array, item => item.brand && item.for_kg === obj.for_kg && item.category_name === obj.category_name);
            }

            return _.filter(data.array, item => !item.brand && item.for_kg === obj.for_kg && item.category_name === obj.category_name);
        };

        if (!_.isEmpty(data)) {
            _.forEach(data, (array) => {
                _.forEach(array, (obj) => {
                    const findedObj = _.find(result, {
                        client_id: obj.client_id,
                        brand: obj.brand,
                        for_kg: obj.for_kg,
                        name: obj.name,
                        category_name: obj.category_name,
                    });

                    if (_.isObject(findedObj)) {
                        _.set(findedObj, 'place', _.add(findedObj.place, obj.place));
                        _.set(findedObj, 'kg', _.add(findedObj.kg, obj.kg));
                        _.set(findedObj, 'sum', _.add(findedObj.sum, obj.sum));
                        _.set(findedObj, 'clientItemsArray', arrayForGroupItem({ findedObj, array }));
                    } else {
                        _.set(obj, 'clientItemsArray', arrayForGroupItem({ obj, array }));

                        result.push(_.clone(obj));
                    }
                });
            });
        }

        // console.log('result874', result);
        await dispatch('setFaxData', result);
    },

    setTableCategoriesData ({ commit }, data) {
        commit('SET_TABLE_CATEGORIES_DATA', data);
    },

    setClientsNames ({ commit }, data) {
        commit('SET_CLIENTS_NAMES', data);
    },

    setCategories ({ commit }, data) {
        commit('SET_CATEGORIES', data);
    },

    setFaxData ({ commit }, data) {
        commit('SET_FAX_DATA', data);
    },
    destroyEntriesFromGroupedData ({ commit, dispatch, state }, objData) {
        commit('DESTROY_ENTRIES_FROM_GROUPED_DATA', objData);
        dispatch('dataAggregation', state.groupedData);
    },

    setFaxNames ({ commit }, data) {
        commit('SET_FAXES_NAMES', data);
    },

    setFaxes ({ commit }, data) {
        commit('SET_FAXES', data);
    },

    addFax ({ commit }, data) {
        commit('ADD_FAX', data);
    },

    destroyFaxes ({ commit }, data) {
        commit('DESTROY_FAXES', data);
    },
    setSearchValue ({ commit }, value) {
        commit('SET_SEARCH_VALUE', value);
    },
    async getCategories ({ dispatch, state }) {
        if (_.isEmpty(state.categories)) {
            try {
                // Запрос данных по категория
                const { data: categoriesData } = await axios.get('faxes/categories');
                const { categories = [] } = categoriesData;
                // console.log('Categories', categories);

                dispatch('setCategories', categories);
            } catch (e) {
                console.error(`Произошла ошибка при запросе данных о категория факса - ${e}`);
            } finally {
                console.log('Completed request for get fax categories.');
            }
        }
    },
    async getClients ({ dispatch, state }) {
        if (_.isEmpty(state.clients)) {
            try {
                // Запрос данных по клиентам
                const { data: clientsData } = await axios.get('users/clients');
                const { clients = [] } = clientsData;
                // console.log('clientsData', clients);
                dispatch('setClientsNames', clients);
            } catch (e) {
                console.error(`Произошла ошибка при запросе данных о клиентах - ${e}`);
            } finally {
                console.log('Completed request for get clients names.');
            }
        }
    },
};
