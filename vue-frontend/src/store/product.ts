import { defineStore } from 'pinia'
import axios from '@/utils/axios'

interface Product {
  id: number
  name: string
  description?: string
  price: number
  stock: number
  sku: string
}

interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

interface ProductState {
  products: Product[]
  pagination: PaginationMeta | null
  product: Product | null
  loading: boolean
  error: string | null
}

export const useProductStore = defineStore('product', {
  state: (): ProductState => ({
    products: [],
    pagination: null,
    product: null,
    loading: false,
    error: null,
  }),

  actions: {
    /**
     * Fetch paginated products from Laravel API
     */
    async fetchProducts(page = 1) {
      this.loading = true
      this.error = null
      try {
        const res = await axios.get(`/products?page=${page}`)
        // Laravel's pagination structure: { data, meta, links }
        const data = res.data.data || res.data
        this.products = data.data || data
        this.pagination = data.meta || {
          current_page: data.current_page,
          last_page: data.last_page,
          per_page: data.per_page,
          total: data.total,
        }
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to load products'
      } finally {
        this.loading = false
      }
    },

    async fetchProduct(id: number) {
      this.loading = true
      try {
        const res = await axios.get(`/products/${id}`)
        this.product = res.data.data || res.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to load product'
      } finally {
        this.loading = false
      }
    },

    async createProduct(payload: Omit<Product, 'id'>) {
      this.loading = true
      try {
        const res = await axios.post('/products', payload)
        await this.fetchProducts()
        return res.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to create product'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateProduct(id: number, payload: Partial<Product>) {
      this.loading = true
      try {
        const res = await axios.put(`/products/${id}`, payload)
        await this.fetchProducts(this.pagination?.current_page || 1)
        return res.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to update product'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteProduct(id: number) {
      this.loading = true
      try {
        await axios.delete(`/products/${id}`)
        await this.fetchProducts(this.pagination?.current_page || 1)
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to delete product'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
