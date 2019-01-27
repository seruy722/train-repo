// state
export const state = () => ({
    defaultFoto: 'nofoto.jpg',
    imageUrl: '/storage/images/',
    logoImg: 'logo.png',
    adminRole: 'admin',
    allClientsObj: {
        id: 0,
        name: 'Все',
    },
});

// getters
export const getters = {
    defaultFoto: state => `${state.imageUrl}${state.defaultFoto}`,
    imageUrl: state => state.imageUrl,
    logoUrl: state => `${state.imageUrl}${state.logoImg}`,
    adminRole: state => state.adminRole,
    allClientsObj: state => state.allClientsObj,
};

// mutations
export const mutations = {};

// actions
export const actions = {};
