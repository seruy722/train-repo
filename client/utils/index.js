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

export class Date {
    constructor () {
        this.month = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
        this.dateFormat = 'DD-MM-YYYY';
    }

    today () {
        return moment().format(this.dateFormat);
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
        return moment(date, this.dateFormat,true).isValid();
    }

    currentMonth () {
        const month = moment().month();
        return `${this.month[month]} ${moment().year()}`;
    }

    currentYear () {
        return `${moment().year()} год`;
    }

    currentWeek () {
        const startOfWeek = moment().startOf('isoweek').format('DD-MM');
        const endOfWeek = moment().endOf('isoweek').format(this.dateFormat);
        return `Нед. ${startOfWeek} - ${endOfWeek}`;
    }

    year(date){
        return `${moment(date).year()} год`;
    }

    choiceWeek(date){
        const monday = moment(date, this.dateFormat).startOf('isoweek').format(this.dateFormat);
        const sunday = moment(date, this.dateFormat).endOf('isoweek').format(this.dateFormat);
        return {monday, sunday};
    }
}
