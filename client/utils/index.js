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
        c => c.trim().startsWith(`${key}=`),
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
        position = { x: 0, y: 0 };
    } else if (to.matched.some(r => r.components.default.options.scrollToTop)) {
        position = { x: 0, y: 0 };
    }
    if (to.hash) {
        position = { selector: to.hash };
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
 * Функция форматирует число в читабельный вид
 * @param number
 * @return {*}
 */
export function numberFormat (number) {
    return new Intl.NumberFormat('ru-RU').format(_.toNumber(number));
}


/**
 * Функция обратная функции numberFormat
 * @param number
 * @return {*}
 */
export function numberUnformat (number) {
    return _.chain(number).toString().split(' ').join('').toNumber();
}

/**
 * Проверяет существование клиента
 *
 * @param array
 * @param clientObj
 * @return {object}
 */
export function isClient (array, clientObj) {
    return _.find(array, clientObj);
}
