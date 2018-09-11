require('./bootstrap');

window.Vue = require('vue');
window.select2 = require('select2');
window.swal = require('sweetalert2/dist/sweetalert2');

Vue.component('select2', require('./components/Select2.vue'))

Object.defineProperty(String.prototype, 'indexOfInsensitive', { value:
	function (s, b) {
		return this.toLowerCase().indexOf((s+'').toLowerCase(), b);
	}
});
