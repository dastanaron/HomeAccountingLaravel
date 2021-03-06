
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Vuex from 'vuex'
import Vuetify from 'vuetify';
import VSwipe from 'vswipe';


Vue.use(Vuex);
Vue.use(Vuetify);
Vue.use(VSwipe);

import privateArea from './components/PrivateArea/PrivateArea.vue';

/**
 * Mobile detected
 * @type {MobileDetect}
 */
let MobileDetect = require('mobile-detect');
let md = new MobileDetect(window.navigator.userAgent);

const mobile = md.mobile();

const store = new Vuex.Store({
    state: {
        preloader: false,

        mobile: mobile,

        alertControl: {
            show: false,
            type: 'success',
            message: '',
        },
        defaultCurrency: 643,
    },
    mutations: {
        setPreloader(state, status=true) {

            if(status === false) {
                setTimeout(() => {
                    state.preloader = status;
                }, 1000)
            }
            else {
                state.preloader = status;
            }

        },
        setAlert(state, object) {
            state.alertControl.type = object.type;
            state.alertControl.show = object.status;
            state.alertControl.message = object.message;
        },
        AlertError(state, error) {
            state.alertControl.type = 'error';
            state.alertControl.show = true;
            state.alertControl.message = 'Ошибка передачи транзакции, попробуйте перезапустить страницу. Ответ сервера: '+error;
        },
        setDefaultCurrency(state, currencyCode) {
            state.defaultCurrency = currencyCode;
        }
    },
    getters: {
        getPreloader: state=> {
            return state.preloader;
        },
        getAlert: state => {
            return state.alertControl;
        },
        mobile: state => {
            return state.mobile;
        },
        getDefaultCurrency: state => {
            return state.defaultCurrency;
        }
    },
});

const app = new Vue({
    el: '#privateArea',
    template: '<private-area></private-area>',
    components: {privateArea},
    store
});
