import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'

export default function admin(
  to: RouteLocationNormalized,
  from: RouteLocationNormalized,
  next: NavigationGuardNext
) {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  if (user.role !== 'admin') {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
}