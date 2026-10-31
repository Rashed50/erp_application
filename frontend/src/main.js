import './bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'
import '@fortawesome/fontawesome-free/css/all.min.css'
import 'vuetify/styles'
import 'sweetalert2/dist/sweetalert2.min.css'
import 'vue3-toastify/dist/index.css'
import '@mdi/font/css/materialdesignicons.css'
import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import App from './App.vue'
import router from './router/index'
import i18n from './i18n'
import { registerImageFallback } from './helpers/imagePlaceholder'
import axios from 'axios'
import { touchSession, useAuthStore } from './stores/auth'

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'light',
  },
})

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(vuetify)
app.use(i18n)

registerImageFallback()

// Every answered request restarts the backend's idle clock, so mirror it here.
// A 401 means the login timed out (or was revoked): sign out and return to the
// login page. Login and logout handle their own 401s.
axios.interceptors.response.use(
  (response) => {
    touchSession()
    return response
  },
  (error) => {
    const url = error.config?.url ?? ''
    // Validation and other errors still reached the backend with a valid token.
    if (error.response && error.response.status !== 401) touchSession()
    if (error.response?.status === 401 && !url.endsWith('/api/login') && !url.endsWith('/api/logout')) {
      useAuthStore().expireSession()
    }
    return Promise.reject(error)
  },
)

app.mount('#app')
