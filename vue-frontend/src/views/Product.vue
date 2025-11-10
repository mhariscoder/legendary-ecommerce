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
            <TableComponent
                v-if="productStore.products.length"
                :columns="columns"
                :data="productStore.products"
                :pagination="productStore.pagination"
                :loading="productStore.loading"
                :error="productStore.error"
                @page-change="handlePageChange"
                @create="openCreateModal"
                @edit="openEditModal"
                @remove="openDeleteModal"
                @bulk="handleBulk"
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

    <!-- Product Create/Update Modal -->
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

                <!-- Product Images Upload -->
                <div class="col-12 mb-4">
                    <h6>Product Images</h6>
                    <images-dragger @getImages="handleMainImages" />
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
                      <div class="row d-flex">
                        <div class="col-md-6">
                          <div class="form-group mb-3">
                            <input
                              v-model="variation.price"
                              type="number"
                              class="form-control"
                              required
                              placeholder="Price"
                            />
                          </div>

                          <div class="form-group mb-3">
                            <input
                              v-model="variation.stock"
                              type="number"
                              class="form-control"
                              required
                              placeholder="Stock"
                            />
                          </div>

                          <div class="form-group mb-3">
                            <select
                            class="form-select"
                            v-model="variation.attribute_values"
                            >
                            <option
                                v-for="attrValue in allAttributeValues"
                                :key="attrValue.id"
                                :value="attrValue.id"
                            >
                                {{ attrValue.attributeName }}: {{ attrValue.value }}
                            </option>
                            </select>

                          </div>

                          <!-- Variation Images -->
                          <div class="form-group">
                            <images-dragger
                              :index="idx"
                              @getImages="handleVariationImages"
                            />
                            <div class="d-flex flex-wrap gap-2 mt-2">
                              <img
                                v-for="(img, i) in variation.images"
                                :key="i"
                                :src="previewImage(img)"
                                alt="Variation image"
                                class="rounded border"
                                style="width: 70px; height: 70px; object-fit: cover;"
                              />
                            </div>
                          </div>
                        </div>

                        <div class="col-md-6 text-end">
                          <button
                            class="btn btn-sm btn-danger"
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

    <!-- Delete Modal -->
    <div
      class="modal fade"
      id="deleteModal"
      tabindex="-1"
      aria-labelledby="deleteModalLabel"
      aria-hidden="true"
      ref="deleteModalRef"
    >
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Product</h5>
            <button type="button" class="btn-close" @click="closeDeleteModal"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to remove <strong>{{ selectedProduct?.name }}</strong>?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeDeleteModal">Cancel</button>
            <button class="btn btn-danger" @click="handleRemove">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, nextTick, onMounted } from 'vue'
import { Modal } from 'bootstrap'
import { useProductStore } from '@/store/product'
import { useAttributeStore } from '@/store/attribute'
import { useUtilsStore } from '@/store/utils'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import TableComponent from '@/components/TableComponent.vue'
import ImagesDragger from '@/components/common/FormImagesDragger.vue'

// Store imports
const productStore = useProductStore()
const attributeStore = useAttributeStore()
const utilsStore = useUtilsStore()

// Refs
const productModalRef = ref(null)
const deleteModalRef = ref(null)
let modalInstance: Modal | null = null
let deleteModalInstance: Modal | null = null

const isEdit = ref(false)
const selectedProduct = ref(null)
const images = ref<File[]>([])

const form = reactive({
  id: null,
  name: '',
  description: '',
  price: '',
  stock: '',
  sku: '',
  variations: [] as any[],
})

onMounted(async () => {
  await productStore.fetchProducts(1)
  await attributeStore.fetchAttributes()
})

const columns = computed(() => {
  const firstItem = productStore.products[0]
  if (!firstItem) return []
  const dynamicObj = Object.keys(firstItem).map(key => ({
    key,
    label: key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
  }))
  dynamicObj.push({ key: 'actions', label: 'Actions' })
  return dynamicObj
})

const allAttributeValues = computed(() => {
  return attributeStore.attributes.flatMap(attr =>
    attr.values.map(value => ({
      id: value.id,          // <-- use the value's id only
      value: value.value,    // the actual string value
      attributeName: attr.name
    }))
  )
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
    variations: [],
  })
  images.value = []
  showProductModal()
}

function openEditModal(product: any) {
  isEdit.value = true
  Object.assign(form, product)
  form.variations = product.variations || []
  showProductModal()
}

function showProductModal() {
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
  form.variations.push({ price: '', stock: '', attribute_values: '', images: [] })
}

function removeVariation(index: number) {
  form.variations.splice(index, 1)
}

// function handleMainImages({ index, files }) {
//   images.value = [...images.value, ...files]  // Update the images array with new files
// }

// function handleVariationImages({ index, files }) {
//   if (form.variations[index]) {
//     form.variations[index].images = files
//   }
// }

function previewImage(img: File | string) {
  if (typeof img === 'string') return img
  return URL.createObjectURL(img)
}

async function handleSubmit() {
  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('description', form.description)
  formData.append('price', form.price)
  formData.append('stock', form.stock)
  formData.append('sku', form.sku)

  //   images.value.forEach((img, i) => formData.append(`images[${i}]`, img))

  // Append main images
  images.value.forEach((url, i) => formData.append(`images[${i}]`, url))



  //   form.variations.forEach((variation, vIndex) => {
//     variation.images?.forEach((img, i) => {
//       formData.append(`variation_images[${vIndex}][]`, img)
//     })
//   })

  // Append variations correctly
  form.variations.forEach((variation, vIndex) => {
    formData.append(`variations[${vIndex}][price]`, variation.price)
    formData.append(`variations[${vIndex}][stock]`, variation.stock)
    formData.append(`variations[${vIndex}][attribute_values]`, variation.attribute_values)

    // Append variation images
    variation.images?.forEach((url, i) => {
      formData.append(`variation_images[${vIndex}][${i}]`, url)
    })
  })

  if (isEdit.value) {
    await productStore.updateProduct(form.id, formData)
  } else {
    await productStore.createProduct(formData)
  }

  closeModal()
  productStore.fetchProducts(productStore.pagination?.current_page || 1)
}

function openDeleteModal(product: any) {
  selectedProduct.value = product
  nextTick(() => {
    if (!deleteModalInstance && deleteModalRef.value) {
      deleteModalInstance = new Modal(deleteModalRef.value)
    }
    deleteModalInstance?.show()
  })
}

function closeDeleteModal() {
  deleteModalInstance?.hide()
}

async function handleRemove() {
  if (selectedProduct.value) {
    await productStore.deleteProduct(selectedProduct.value.id)
    closeDeleteModal()
    productStore.fetchProducts(productStore.pagination?.current_page || 1)
  }
}

async function handleMainImages({ files }) {
  try {
    const urls = await utilsStore.uploadMultiple(files)
    images.value.push(...urls)
  } catch (err) {
    console.error('Upload error:', err)
  }
}

async function handleVariationImages({ index, files }) {
  try {
    const uploadedUrls = await utilsStore.uploadMultiple(files)

    if (!form.variations[index].images) {
      form.variations[index].images = []
    }

    form.variations[index].images.push(...uploadedUrls)
  } catch (err) {
    console.error('Variation image upload failed:', err)
  }
}
</script>

<style>
.uploader-box {
  border-style: dashed;
  background-color: transparent;
  border-color: #d8e2ef;
}
</style>
