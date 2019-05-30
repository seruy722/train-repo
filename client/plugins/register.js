import Vue from 'vue';
import { needFormatDate, needFormatDateWithTime } from '~/utils/dates';

Vue.filter('formatDate', value => needFormatDate(value) || '00-00-0000');
Vue.filter('formatDateWithTime', value => needFormatDateWithTime(value) || '00-00-0000');
Vue.filter('upperFirst', value => _.upperFirst(value));
Vue.filter('convertCommonItems', value => value ? 'ДА' : 'НЕТ');
