import { defineStore } from 'pinia'
import axios from '../utils/axios'

export const useMainStore = defineStore('main', {
  state: () => ({
    user: null,
  }),
  actions: {
    async login(credentials) {
      const res = await axios.post('/login', credentials)
      this.user = res.data.user
      localStorage.setItem('token', res.data.token)
    },
    logout() {
      this.user = null
      localStorage.removeItem('token')
    },
  },
})