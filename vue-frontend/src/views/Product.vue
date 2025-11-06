<template>
  <DefaultLayout>
    <div class="d-flex">
      <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Products</h2>

        <div class="card">
          <div class="card-header">
            <div class="row flex-between-end">
              <div class="col-auto align-self-center">
                <h5 class="mb-0">Product List</h5>
              </div>
            </div>
          </div>

          <div class="card-body py-0 border-top">
            <div class="card shadow-none">
              <div class="card-body p-0 pb-3">
                <!-- ✅ Dynamic Table -->
                <TableComponent
                  v-if="productStore.products.length"
                  :columns="columns"
                  :data="productStore.products"
                  :pagination="productStore.pagination"
                  :loading="productStore.loading"
                  :error="productStore.error"
                  @page-change="handlePageChange"
                  @create="handleCreate"
                  @bulk="handleBulk"
                />

                <!-- Loading and empty states -->
                <div v-else-if="productStore.loading" class="text-center py-5 text-muted">
                  <div class="spinner-border text-primary" role="status"></div>
                  <div class="mt-2">Loading products...</div>
                </div>

                <div v-else class="text-center py-5 text-muted">
                  No products found.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import TableComponent from '@/components/TableComponent.vue'
import { useProductStore } from '@/store/product'

const productStore = useProductStore()

onMounted(() => {
  productStore.fetchProducts(1)
})

const columns = computed(() => {
  const firstItem = productStore.products[0]
  if (!firstItem) return []
  return Object.keys(firstItem).map(key => ({
    key,
    label: key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
  }))
})

function handlePageChange(page: number) {
  productStore.fetchProducts(page)
}

function handleCreate() {
  alert('Create new product - open modal or redirect to create form')
}

function handleBulk(action: string) {
  alert(`Performing bulk action: ${action}`)
}
</script>
