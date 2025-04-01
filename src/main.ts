import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import MedicationAdminPlugin from './plugins/medication-admin'

const app = createApp(App)
app.use(router)
app.use(MedicationAdminPlugin)
app.mount('#app')