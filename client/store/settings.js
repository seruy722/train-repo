// state
export const state = () => ({
    defaultFoto: 'nofoto.jpg',
    imageUrl: '/storage/images/',
    logoImg: 'logo.png'
});

// getters
export const getters = {
    defaultFoto: state => `${state.imageUrl}${state.defaultFoto}`,
    imageUrl: state => state.imageUrl,
    logoUrl: state => `${state.imageUrl}${state.logoImg}`
};

// mutations
export const mutations = {};

// actions
export const actions = {};
