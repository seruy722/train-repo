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
});

// getters
export const getters = {
    defaultFoto: state => `${state.imageUrl}${state.defaultFoto}`,
    imageUrl: state => state.imageUrl,
    logoUrl: state => `${state.imageUrl}${state.logoImg}`,
    adminRole: state => state.adminRole,
    allClientsObj: state => state.allClientsObj,
    selectDatesItems: state => state.selectDatesItems,
};
