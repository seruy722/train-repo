import Vue from 'vue';
import { needFormatDate } from '~/utils/dates';

Vue.filter('formatDate', value => needFormatDate(value) || '00-00-0000');
Vue.filter('upperFirst', value => _.upperFirst(value));
