import { defineStore } from 'pinia'
import axios from '@/utils/axios'

interface UserState {
  email: string
  token: string | null
  loading: boolean
}

export const useMainStore = defineStore('main', {
  state: (): UserState => ({
    email: '',
    token: localStorage.getItem('token') || null,
    loading: false,
  }),

  actions: {
    async login(payload: { email: string; password: string }) {
      this.loading = true
      try {
        const res = await axios.post('/login', payload)
        this.token = res.data.data.token
        localStorage.setItem('token', res.data.data.token)
      } finally {
        this.loading = false
      }
    },
    async register(payload: { name: string; email: string; password: string }) {
      this.loading = true
      try {
        const res = await axios.post('/register', payload)
        
        this.token = res.data.data.token

        localStorage.setItem('token', res.data.data.token)

      } catch (error) {
        throw new Error(error.response?.data?.message || 'Something went wrong')
      } finally {
        this.loading = false
      }
    },
    async forgotPassword(email: string) {
      await axios.post('/forgot-password', { email })
    },
    logout() {
      this.token = null
      localStorage.removeItem('token')
    },
  },
})