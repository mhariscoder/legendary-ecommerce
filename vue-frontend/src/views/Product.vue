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
                <TableComponent
                  v-if="productStore.products.length"
                  :columns="columns"
                  :data="productStore.products"
                  :pagination="productStore.pagination"
                  :loading="productStore.loading"
                  :error="productStore.error"
                  @page-change="handlePageChange"
                  @create="openCreateModal"
                  @bulk="handleBulk"
                  @row-click="openEditModal"
                />

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

    <!-- ✅ Product Modal -->
    <div
      class="modal fade"
      id="productModal"
      tabindex="-1"
      aria-labelledby="productModalLabel"
      aria-hidden="true"
      ref="productModalRef"
    >
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="productModalLabel">
              {{ isEdit ? 'Update Product' : 'Create Product' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <form @submit.prevent="handleSubmit">
              <div class="row">
                <!-- Basic Info -->
                <div class="col-md-6 mb-3">
                  <label class="form-label">Product Name</label>
                  <input v-model="form.name" type="text" class="form-control" required />
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">SKU</label>
                  <input v-model="form.sku" type="text" class="form-control" required />
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Price</label>
                  <input v-model="form.price" type="number" class="form-control" required />
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Stock</label>
                  <input v-model="form.stock" type="number" class="form-control" required />
                </div>

                <div class="col-md-12 mb-3">
                  <label class="form-label">Description</label>
                  <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                </div>

                <!-- ✅ Live Image Upload -->
                <div class="col-12 mb-4">
                  <h6 class="border-bottom pb-2">Product Images</h6>
                  <vue-multi-image-upload
                    @data-image="onImagesSelected"
                    :max="5"
                    :image-size="5e6"
                    :accept="['image/jpeg','image/png']"
                    :preview="{ h: 120, w: 120 }"
                    :resize="{ h: 800, w: 800, keepRatio: true }"
                  />
                  <small class="text-muted">You can upload up to 5 images (JPEG/PNG only).</small>
                </div>

                <!-- Attributes -->
                <div class="col-12 mb-3">
                  <h6 class="border-bottom pb-2">Attributes</h6>
                  <div v-if="attributes.length">
                    <div
                      v-for="attr in attributes"
                      :key="attr.id"
                      class="mb-3 border p-3 rounded"
                    >
                      <label class="form-label fw-semibold">{{ attr.name }}</label>
                      <select
                        multiple
                        class="form-select"
                        v-model="form.selectedAttributes[attr.id]"
                      >
                        <option
                          v-for="value in attr.values"
                          :key="value.id"
                          :value="value.id"
                        >
                          {{ value.value }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div v-else class="text-muted">No attributes available.</div>
                </div>

                <!-- Variations -->
                <div class="col-12">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Variations</h6>
                    <button
                      class="btn btn-sm btn-outline-primary"
                      type="button"
                      @click="addVariation"
                    >
                      <i class="fas fa-plus me-1"></i> Add Variation
                    </button>
                  </div>

                  <div v-if="form.variations.length">
                    <div
                      v-for="(variation, idx) in form.variations"
                      :key="idx"
                      class="border rounded p-3 mb-3"
                    >
                      <div class="row align-items-end">
                        <div class="col-md-4">
                          <label class="form-label">Price</label>
                          <input
                            v-model="variation.price"
                            type="number"
                            class="form-control"
                            required
                          />
                        </div>

                        <div class="col-md-4">
                          <label class="form-label">Stock</label>
                          <input
                            v-model="variation.stock"
                            type="number"
                            class="form-control"
                            required
                          />
                        </div>

                        <div class="col-md-3">
                          <label class="form-label">Attributes</label>
                          <select
                            multiple
                            class="form-select"
                            v-model="variation.attribute_values"
                          >
                            <option
                              v-for="attrValue in allAttributeValues"
                              :key="attrValue.id"
                              :value="attrValue.id"
                            >
                              {{ attrValue.attribute.name }}: {{ attrValue.value }}
                            </option>
                          </select>
                        </div>

                        <div class="col-md-1 text-end">
                          <button
                            class="btn btn-sm btn-danger mt-2"
                            type="button"
                            @click="removeVariation(idx)"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-muted">No variations added yet.</div>
                </div>
              </div>

              <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary me-2" @click="closeModal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="productStore.loading">
                  <span
                    v-if="productStore.loading"
                    class="spinner-border spinner-border-sm me-2"
                  ></span>
                  {{ isEdit ? 'Update Product' : 'Create Product' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import TableComponent from '@/components/TableComponent.vue'
import { useProductStore } from '@/store/product'
import { Modal } from 'bootstrap'
// import { VueMultiImageUpload } from '@zakerxa/vue-multiple-image-upload'

const productStore = useProductStore()
const productModalRef = ref(null)
let modalInstance: Modal | null = null
const isEdit = ref(false)

const attributes = ref<any[]>([])
const allAttributeValues = ref<any[]>([])
const uploadedImages = ref<File[]>([])

const form = reactive({
  id: null,
  name: '',
  description: '',
  price: '',
  stock: '',
  sku: '',
  selectedAttributes: {} as Record<number, number[]>,
  variations: [] as any[],
})

onMounted(async () => {
  await productStore.fetchProducts(1)
  await fetchAttributes()
})

async function fetchAttributes() {
  const res = await fetch('/api/product-attributes')
  const data = await res.json()
  attributes.value = data
  allAttributeValues.value = data.flatMap((attr: any) =>
    attr.values.map((v: any) => ({
      ...v,
      attribute: { id: attr.id, name: attr.name },
    }))
  )
}

function onImagesSelected(files: File[]) {
  uploadedImages.value = files
}

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

function handleBulk(action: string) {
  alert(`Performing bulk action: ${action}`)
}

function openCreateModal() {
  isEdit.value = false
  Object.assign(form, {
    id: null,
    name: '',
    description: '',
    price: '',
    stock: '',
    sku: '',
    selectedAttributes: {},
    variations: [],
  })
  uploadedImages.value = []
  showModal()
}

function openEditModal(product: any) {
  isEdit.value = true
  Object.assign(form, product)
  showModal()
}

function showModal() {
  nextTick(() => {
    if (!modalInstance && productModalRef.value) {
      modalInstance = new Modal(productModalRef.value)
    }
    modalInstance?.show()
  })
}

function closeModal() {
  modalInstance?.hide()
}

function addVariation() {
  form.variations.push({ price: '', stock: '', attribute_values: [] })
}

function removeVariation(index: number) {
  form.variations.splice(index, 1)
}

async function handleSubmit() {
  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('description', form.description)
  formData.append('price', form.price)
  formData.append('stock', form.stock)
  formData.append('sku', form.sku)

  uploadedImages.value.forEach(file => formData.append('images[]', file))

  formData.append('attributes', JSON.stringify(form.selectedAttributes))
  formData.append('variations', JSON.stringify(form.variations))

  if (isEdit.value) await productStore.updateProduct(formData)
  else await productStore.createProduct(formData)

  closeModal()
  productStore.fetchProducts(productStore.pagination?.current_page || 1)
}
</script>

<style scoped>
.modal-body {
  max-height: 80vh;
  overflow-y: auto;
}
</style>
