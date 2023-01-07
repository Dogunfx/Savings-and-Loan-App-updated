window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Vue = require('vue');
import VueFlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

Vue.use(VueFlatPickr);
import vSelect from 'vue-select'

Vue.component('v-select', vSelect)
import Editor from '@tinymce/tinymce-vue';
// commonjs require
// NOTE: default needed after require
Vue.component('editor', Editor)
