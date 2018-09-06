require('./bootstrap');

window.Vue = require('vue');
window.select2 = require('select2');

Vue.component('select2', require('./components/Select2.vue'))
