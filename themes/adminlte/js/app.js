window._ = require('lodash');
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
window.Vue = require('vue');
window.GridStack = require('gridstack/dist/gridstack-h5.js');
window.toastr = require('toastr');
import VueFlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

Vue.use(VueFlatPickr);
import vSelect from 'vue-select'

Vue.component('v-select', vSelect)
let $=require('jquery')
window.$ = window.jQuery = $;
import 'overlayscrollbars/js/jquery.overlayScrollbars.js';
import 'jquery-ui-sortable-npm/jquery-ui-sortable.min';
require('popper.js');
require('bootstrap')
require('admin-lte')
var dt      = require( 'datatables.net-bs4' );
const Swal = require('sweetalert2')
window.Swal=Swal
import '@fortawesome/fontawesome-free/js/all.js';

