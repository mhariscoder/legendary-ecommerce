import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import { toastContainers, toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import router from './router'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.component('ToastContainer', toastContainers)

app.mount('#app')