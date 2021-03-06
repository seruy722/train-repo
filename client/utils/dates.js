import moment from 'moment';

// const month = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
const dateFormat = 'DD-MM-YYYY';

/**
 *
 * @param obj
 * @return {*}
 */
export function needDate (obj) {
    return _.forEach(obj, (result, item, index) => {
        if (moment(item, 'YYYY-MM-DD HH:mm:ss').isValid()) {
            result[index] = item.substr(0, 10).split('-').reverse().join('-');
        }

        result[index] = item;

        return result;
    }, {});
}

export const reverseDate = date => new Date(date).toLocaleDateString().replace(/\./g, '-');


export const getDateFromStr = () => new Date().toJSON().substr(0, 10);

/**
 * Возвращает текущую дату
 *
 * @return {string}
 */
export const today = () => new Date().toLocaleDateString().split('.').reverse().join('-');


export const formatDateToServerDate = (date) => {
    if (isDate(date)) {
        // const needDate = new Date(`${date} ${timeNow()}`);
        // console.log('needDate', needDate);
        // return `${needDate.toJSON().substr(0, 10)}`;
        return new Date(`${date} ${timeNow()}`).toISOString();
    }
    return date;
};


function timeNow () {
    return new Date().toLocaleTimeString();
}

/**
 * Возвращает текущую дату
 *
 * @return {string}
 */
export const notFormatedToday = () => {
    const dateToday = new Date();
    return `${dateToday.toISOString().substr(0, 10)} ${dateToday.toLocaleTimeString()}`;
};

/**
 *Возвращает отформатированную дату
 *
 * @param date
 * @return {*}
 */
export const needFormatDate = date => isDate(date) ? new Date(date).toLocaleDateString().replace(/\./g, '-') : date;
export const needFormatDateWithTime = date => isDate(date) ? moment(date).format('DD-MM-YYYY HH:mm:ss') : date;

/**
 * Проверяет, что это дата
 *
 * @param date
 * @return {boolean}
 */
export const isDate = date => !_.isNaN(Date.parse(date));

/**
 * Возвращает завтрашнюю дату
 *
 * @return {string}
 */
export function tomorrow () {
    return moment(new Date()).add(1, 'days').format(dateFormat);
}

/**
 * Возвращает вчерашнюю дату
 *
 * @return {string}
 */
export function yesterday () {
    return moment(new Date()).add(-1, 'days').format(dateFormat);
}

/**
 * Возвращает следующую дату от переданной
 *
 * @param date
 * @return {*}
 */
export function nextDate (date) {
    if (isValid(date)) {
        return moment(date, dateFormat).add(1, 'days').format(dateFormat);
    }
    return false;
}

/**
 * Возвращает предыдущюю дату от переданной
 *
 * @param date
 * @return {*}
 */
export function prevDate (date) {
    if (isValid(date)) {
        return moment(date, dateFormat).add(-1, 'days').format(dateFormat);
    }
    return false;
}

/**
 * Возвращает начальную и конечную дату текущего месяца
 *
 * @return {{startDate: string, endDate: string}}
 */
export function currentMonth () {
    const startOfMonth = moment().startOf('month').format(dateFormat);
    const endOfMonth = moment().endOf('month').format(dateFormat);
    return {
        startDate: startOfMonth,
        endDate: endOfMonth,
    };
}

/**
 * Возвращает начальную и конечную дату переданного месяца
 *
 * @param monthYear
 * @return {{startDate: string, endDate: string}}
 */
export function startEndDateOfMonth (monthYear) {
    const startOfMonth = moment(monthYear, 'MM-YYYY').startOf('month').format(dateFormat);
    const endOfMonth = moment(monthYear, 'MM-YYYY').endOf('month').format(dateFormat);
    return {
        startDate: startOfMonth,
        endDate: endOfMonth,
    };
}

/**
 * Возвращает начальную и конечную дату текущего года
 *
 * @return {{startDate: string, endDate: string}}
 */
export function currentYear () {
    const startOfYear = moment().startOf('year').format(dateFormat);
    const endOfYear = moment().endOf('year').format(dateFormat);
    return {
        startDate: startOfYear,
        endDate: endOfYear,
    };
}

/**
 * Возвращает начальную и конечную дату переданного года
 *
 * @param year
 * @return {{startDate: string, endDate: string}}
 */
export function startEndDateOfYear (year) {
    const startOfYear = moment(year, 'YYYY').startOf('year').format(dateFormat);
    const endOfYear = moment(year, 'YYYY').endOf('year').format(dateFormat);
    return {
        startDate: startOfYear,
        endDate: endOfYear,
    };
}

/**
 * Возвращает начальную и конечную дату текущей недели
 *
 * @return {{startDate: string, endDate: string}}
 */
export function currentWeek () {
    const startOfWeek = moment().startOf('isoweek').format(dateFormat);
    const endOfWeek = moment().endOf('isoweek').format(dateFormat);
    return {
        startDate: startOfWeek,
        endDate: endOfWeek,
    };
}

/**
 * Возвращает начальную и конечную дату недели от переданной даты
 *
 * @param date
 * @return {*}
 */
export function choiceWeek (date) {
    if (!date) {
        return false;
    }
    const startDate = moment(date, dateFormat).startOf('isoweek').format(dateFormat);
    const endDate = moment(date, dateFormat).endOf('isoweek').format(dateFormat);
    return { startDate, endDate };
}

/**
 * Проверяет дату на валидность
 *
 * @param date
 * @return {boolean}
 */
export function isValid (date) {
    return moment(date, dateFormat, true).isValid();
}
