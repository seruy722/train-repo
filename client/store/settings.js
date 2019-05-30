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
    selectDatesItems: [
        {
            id: 1,
            title: 'Все даты',
        },
        {
            id: 2,
            title: 'Сегодня',
        },
        {
            id: 3,
            title: 'Текущий месяц',
        },
        {
            id: 4,
            title: 'Текущий год',
        },
        {
            id: 5,
            title: 'Текущая неделя',
        },
        {
            id: 6,
            title: 'Выбрать период',
        },
        {
            id: 7,
            title: 'Выбрать день',
        },
        {
            id: 8,
            title: 'Выбрать год',
        },
        {
            id: 9,
            title: 'Выбрать месяц',
        },
        {
            id: 10,
            title: 'Выбрать неделю',
        },
    ],
    transportItems: [
        {
            id: 0,
            title: 'Машина',
        }, {
            id: 1,
            title: 'Авиа',
        },
    ],
    commonItems: [
        {
            id: 0,
            title: 'НЕТ',
        }, {
            id: 1,
            title: 'ДА',
        },
    ],
    pageSettings: {
        title: 'Главная',
        icon: 'home',
    },
});

// getters
export const getters = {
    defaultFoto: state => `${state.imageUrl}${state.defaultFoto}`,
    imageUrl: state => state.imageUrl,
    logoUrl: state => `${state.imageUrl}${state.logoImg}`,
    adminRole: state => state.adminRole,
    allClientsObj: state => state.allClientsObj,
    selectDatesItems: state => state.selectDatesItems,
    transportItems: state => state.transportItems,
    commonItems: state => state.commonItems,
    pageSettings: state => state.pageSettings,
};

export const mutations = {
    SET_PAGE_SETTINGS (state, settings) {
        state.pageSettings = settings;
    },
};

export const actions = {
    setPageSettings ({ commit }, settings) {
        commit('SET_PAGE_SETTINGS', settings);
    },
};
