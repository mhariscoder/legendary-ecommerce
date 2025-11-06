import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'

export default function guest(
    to: RouteLocationNormalized,
    from: RouteLocationNormalized,
    next: NavigationGuardNext
) {
        const token = localStorage.getItem('token')

        if (token) {
            next({ name: 'Dashboard' })
        } else {
            next()
        }
}