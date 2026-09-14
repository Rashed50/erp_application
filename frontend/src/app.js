import './bootstrap';
import { createPinia } from "pinia";
import { createApp } from "vue";
import router from './router';

// custom component import
// import DualScroll from '@/components/common/DualScroll.vue'

// Vuetify
import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

const vuetify = createVuetify({
    components,
    directives,
});

// import sweetalert plugin
import VueSweetAlert from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

// Toast
import Toast from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

import App from "./App.vue";

const app = createApp(App);
app.use(createPinia());

app.use(router);
app.use(vuetify);
app.use(VueSweetAlert);
// Toast
// use default export, position as string
app.use(Toast, {
  position: 'top-right',
  autoClose: 1500,
})

// custom component register
// app.component('DualScroll', DualScroll)


app.mount("#app");
