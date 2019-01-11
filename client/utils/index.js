import moment from 'moment';

/**
 * Get cookie from request.
 *
 * @param  {Object} req
 * @param  {String} key
 * @return {String|undefined}
 */
export function cookieFromRequest (req, key) {
    if (!req.headers.cookie) {
        return;
    }

    const cookie = req.headers.cookie.split(';').find(
        c => c.trim().startsWith(`${key}=`)
    );

    if (cookie) {
        return cookie.split('=')[1];
    }
}

/**
 * https://router.vuejs.org/en/advanced/scroll-behavior.html
 */
export function scrollBehavior (to, from, savedPosition) {
    if (savedPosition) {
        return savedPosition;
    }

    let position = {};

    if (to.matched.length < 2) {
        position = {x: 0, y: 0};
    } else if (to.matched.some(r => r.components.default.options.scrollToTop)) {
        position = {x: 0, y: 0};
    }
    if (to.hash) {
        position = {selector: to.hash};
    }

    return position;
}

/**
 * Format date from database
 *
 * @param  {Array|Object} data
 * @param  {String} needFormat
 * @param  {String} enterFormat
 * @return {Array}
 */

export function formatDate (data, enterFormat, needFormat) {
    if (_.isArray(data)) {
        _.forEach(data, (item) => {
            item.created_at = moment(item.created_at, enterFormat).format(needFormat);
        });
    } else {
        data.created_at = moment(data.created_at, enterFormat).format(needFormat);
    }

    return data;
}


/**
 * Class for work with dates
 *
 */
export class DateClass {
    constructor () {
        this.month = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        this.dateFormat = 'DD-MM-YYYY';
    }

    today () {
        return new Date().toISOString().substr(0, 10).split('-').reverse().join('-');
    }

    tomorrow () {
        return moment(new Date()).add(1, 'days').format(this.dateFormat);
    }

    yesterday () {
        return moment(new Date()).add(-1, 'days').format(this.dateFormat);
    }

    next (date) {
        return moment(date, this.dateFormat).add(1, 'days').format(this.dateFormat);
    }

    prev (date) {
        return moment(date, this.dateFormat).add(-1, 'days').format(this.dateFormat);
    }

    isValid (date) {
        return moment(date, this.dateFormat, true).isValid();
    }

    currentMonth () {
        const startOfMonth = moment().startOf('month').format(this.dateFormat);
        const endOfMonth = moment().endOf('month').format(this.dateFormat);
        return {
            startDate: startOfMonth,
            endDate: endOfMonth,
        };
    }

    startEndDateOfMonth (month) {
        const startOfMonth = moment(month, 'MM').startOf('month').format(this.dateFormat);
        const endOfMonth = moment(month, 'MM').endOf('month').format(this.dateFormat);
        return {
            startDate: startOfMonth,
            endDate: endOfMonth,
        };
    }

    currentYear () {
        const startOfYear = moment().startOf('year').format(this.dateFormat);
        const endOfYear = moment().endOf('year').format(this.dateFormat);
        return {
            startDate: startOfYear,
            endDate: endOfYear,
        };
    }

    startEndDateOfYear (year) {
        const startOfYear = moment(year, 'YYYY').startOf('year').format(this.dateFormat);
        const endOfYear = moment(year, 'YYYY').endOf('year').format(this.dateFormat);
        return {
            startDate: startOfYear,
            endDate: endOfYear,
        };
    }

    currentWeek () {
        const startOfWeek = moment().startOf('isoweek').format(this.dateFormat);
        const endOfWeek = moment().endOf('isoweek').format(this.dateFormat);
        return {
            startDate: startOfWeek,
            endDate: endOfWeek,
        };
    }

    choiceWeek (date) {
        if (!date) {
            return false;
        }
        const monday = moment(date, this.dateFormat).startOf('isoweek').format(this.dateFormat);
        const sunday = moment(date, this.dateFormat).endOf('isoweek').format(this.dateFormat);
        return { monday, sunday };
    }
}

/**
 * Функция форматирует число в читабельный вид
 * @param number
 * @return {*}
 */
export function numberFormat (number) {
    return new Intl.NumberFormat('ru-RU').format(number);
}


/**
 * Функция обратная функции numberFormat
 * @param number
 * @return {*}
 */
export function numberUnformat (number) {
    const result = number + '';
    result.split(' ').join('');
    return +result;
}
