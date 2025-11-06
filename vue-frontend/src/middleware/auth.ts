import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'

export default function auth(
  to: RouteLocationNormalized,
  from: RouteLocationNormalized,
  next: NavigationGuardNext
) {
  const token = localStorage.getItem('token')

  if (!token) {
    next({ name: 'Login' })
  } else {
    next()
  }
}