<script setup lang="ts">
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { Modal } from 'bootstrap'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import TableComponent from '@/components/TableComponent.vue'
import { useAttributeStore } from '@/store/attribute'

const attributeStore = useAttributeStore()

interface Column {
  key: string;
  label: string;
  render?: (row: any) => string;
}

const attributes = computed(() => attributeStore.attributes)
const loading = computed(() => attributeStore.loading)
const isEdit = ref(false)
const attributeModalRef = ref<HTMLElement | null>(null)
const deleteModalRef = ref<HTMLElement | null>(null)
let modalInstance: Modal | null = null
let deleteModalInstance: Modal | null = null
const selectedAttribute = ref(null)

const form = reactive({
  id: null as number | null,
  name: '',
  values: [] as string[],
})

onMounted(async () => {
  await attributeStore.fetchAttributes()

  nextTick(() => {
    if (attributeModalRef.value) {
      modalInstance = new Modal(attributeModalRef.value)
    }
    if (deleteModalRef.value) {
      deleteModalInstance = new Modal(deleteModalRef.value)
    }
  })
})

const columns = computed(() => [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'actions', label: 'Actions'}
])

function openCreateModal() {
  isEdit.value = false
  Object.assign(form, { id: null, name: '', values: [] })
  showModal()
}

function openEditModal(attribute: any) {
  isEdit.value = true
  Object.assign(form, {
    id: attribute.id,
    name: attribute.name,
    values: attribute.values.map((value: any) => value.value)
  })
  showModal()
}

function openDeleteModal(attribute: any) {
  selectedAttribute.value = attribute
  if (deleteModalInstance) {
    deleteModalInstance.show()
  }
}

function showModal() {
  if (modalInstance && !modalInstance._isShown) {
    modalInstance.show()
  }
}

function closeModal() {
  if (modalInstance) {
    modalInstance.hide()
  }
}

function closeDeleteModal() {
  if (deleteModalInstance) {
    deleteModalInstance.hide()
  }
  selectedAttribute.value = null
}

function addValue() {
  form.values.push('')
}

function removeValue(index: number) {
  form.values.splice(index, 1)
}

async function handleSubmit() {
  const payload = { name: form.name, values: form.values }

  try {
    if (isEdit.value && form.id) {
      await attributeStore.updateAttribute(form.id, payload)
    } else {
      await attributeStore.createAttribute(payload)
    }

    closeModal()
  } catch (err) {
    console.error('❌ Failed to save attribute:', err)
  }
}

async function handleRemove() {
  if (!selectedAttribute.value) return

  try {
    await attributeStore.deleteAttribute(selectedAttribute.value.id)

    closeDeleteModal()

    await attributeStore.fetchAttributes()
  } catch (err) {
    console.error('❌ Failed to remove attribute:', err)
  }
}

function applyBulkAction() {}

function applySearch() {}

function goToPage() {}
</script>

<template>
  <DefaultLayout>
    <div class="d-flex">
      <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Product Attributes</h2>

        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Attribute List</h5>
          </div>

          <div class="card-body py-0 border-top">
            <TableComponent
              v-if="attributes.length"
              :columns="columns"
              :data="attributes"
              :loading="loading"
              @create="openCreateModal"
              @edit="openEditModal"
              @remove="openDeleteModal"
              @bulk="applyBulkAction"
              @search="applySearch"
              @page-change="goToPage"
            />

            <div v-else-if="loading" class="text-center py-5 text-muted">
              <div class="spinner-border text-primary" role="status"></div>
              <div class="mt-2">Loading attributes...</div>
            </div>

            <div v-else class="text-center py-5 text-muted">
              No attributes found.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Create / Update Modal -->
    <div
      class="modal fade"
      id="attributeModal"
      tabindex="-1"
      aria-labelledby="attributeModalLabel"
      aria-hidden="true"
      ref="attributeModalRef"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="attributeModalLabel">
              {{ isEdit ? 'Update Attribute' : 'Create Attribute' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label class="form-label">Attribute Name</label>
                <input v-model="form.name" type="text" class="form-control" required />
              </div>

              <!-- Attribute Values -->
              <div class="border-top pt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="mb-0">Values</h6>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-primary"
                    @click="addValue"
                  >
                    <i class="fas fa-plus me-1"></i> Add Value
                  </button>
                </div>

                <div v-if="form.values.length">
                  <div
                    v-for="(value, idx) in form.values"
                    :key="idx"
                    class="d-flex align-items-center mb-2"
                  >
                    <input
                      v-model="form.values[idx]"
                      type="text"
                      class="form-control me-2"
                      placeholder="Enter value (e.g., Red, Large)"
                      required
                    />
                    <button
                      type="button"
                      class="btn btn-sm btn-danger"
                      @click="removeValue(idx)"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
                <div v-else class="text-muted small">No values added yet.</div>
              </div>

              <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary me-2" @click="closeModal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm me-2"
                  ></span>
                  {{ isEdit ? 'Update' : 'Create' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ Remove Modal -->
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
            <h5 class="modal-title" id="deleteModalLabel">Delete Attribute</h5>
            <button type="button" class="btn-close" @click="closeDeleteModal"></button>
          </div>

          <div class="modal-body">
            <p>Are you sure you want to remove this attribute: <strong>{{ selectedAttribute?.name }}</strong>?</p>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeDeleteModal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="handleRemove">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<style scoped>
.modal-body {
  max-height: 80vh;
  overflow-y: auto;
}
</style>
