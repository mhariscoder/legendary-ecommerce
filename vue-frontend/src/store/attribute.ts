import { defineStore } from 'pinia'
import axios from '@/utils/axios'

interface Attribute {
  id: number
  name: string
  slug?: string
  values?: string[]
}

interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

interface AttributeState {
  attributes: Attribute[]
  attribute: Attribute | null
  pagination: PaginationMeta | null
  loading: boolean
  error: string | null
}

export const useAttributeStore = defineStore('attribute', {
  state: (): AttributeState => ({
    attributes: [],
    attribute: null,
    pagination: null,
    loading: false,
    error: null,
  }),

  actions: {
    /**
     * Get all attributes with pagination
     */
    async fetchAttributes(page = 1) {
      this.loading = true
      this.error = null
      try {
        const res = await axios.get(`/product-attributes?page=${page}`)
        const data = res.data.data || res.data

        this.attributes = data.data || data
        this.pagination = data.meta || {
          current_page: data.current_page,
          last_page: data.last_page,
          per_page: data.per_page,
          total: data.total,
        }
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to load attributes'
      } finally {
        this.loading = false
      }
    },

    /**
     * Get single attribute by ID
     */
    async fetchAttribute(id: number) {
      this.loading = true
      try {
        const res = await axios.get(`/product-attributes/${id}`)
        this.attribute = res.data.data || res.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to load attribute'
      } finally {
        this.loading = false
      }
    },

    /**
     * Create a new attribute
     */
    async createAttribute(payload: Omit<Attribute, 'id'>) {
      this.loading = true
      try {
        const res = await axios.post('/product-attributes', payload)
        await this.fetchAttributes()
        return res.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to create attribute'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Update attribute
     */
    async updateAttribute(id: number, payload: Partial<Attribute>) {
      this.loading = true
      try {
        const res = await axios.put(`/product-attributes/${id}`, payload)
        await this.fetchAttributes(this.pagination?.current_page || 1)
        return res.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to update attribute'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Delete attribute
     */
    async deleteAttribute(id: number) {
      this.loading = true
      try {
        await axios.delete(`/product-attributes/${id}`)
        await this.fetchAttributes(this.pagination?.current_page || 1)
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to delete attribute'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
