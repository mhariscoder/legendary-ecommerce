import { createRouter, createWebHashHistory } from 'vue-router'

// Views
import Home from '@/views/Home.vue'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import ForgotPassword from '@/views/ForgotPassword.vue'
import Dashboard from '@/views/Dashboard.vue'
import Product from '@/views/Product.vue'
import NotFound from '@/views/NotFound.vue'

// Middlewares
import auth from '@/middleware/auth'
import guest from '@/middleware/guest'


const routes = [
  { path: '/', name: 'Home', component: Home },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { middleware: [guest] },
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { middleware: [guest] },
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: ForgotPassword,
    meta: { middleware: [guest] },
  },

  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { middleware: [auth] },
  },
  {
    path: '/product',
    name: 'Product',
    component: Product,
    meta: { middleware: [auth] },
  },

  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
})

/**
 * Global middleware pipeline
 */
router.beforeEach(async (to, from, next) => {
  if (!to.meta.middleware) {
    return next()
  }

  const middlewares = Array.isArray(to.meta.middleware)
    ? to.meta.middleware
    : [to.meta.middleware]

  const context = { to, from, next }

  // Run middlewares sequentially
  const nextMiddleware = (index: number) => {
    const current = middlewares[index]
    if (!current) return next()
    current(to, from, (params?: any) => {
      if (params !== undefined) return next(params)
      nextMiddleware(index + 1)
    })
  }

  nextMiddleware(0)
})

export default router
