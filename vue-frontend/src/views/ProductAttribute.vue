<template>
  <DefaultLayout>
    <div class="d-flex">
      <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Product Attributes</h2>

        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Attribute List</h5>
            <button class="btn btn-primary btn-sm" @click="openCreateModal">
              <i class="fas fa-plus me-1"></i> Add Attribute
            </button>
          </div>

          <div class="card-body p-0">
            <TableComponent
              v-if="attributes.length"
              :columns="columns"
              :data="attributes"
              :loading="loading"
              @row-click="openEditModal"
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

    <!-- ✅ Modal -->
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
                      v-model="value.value"
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
  </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { Modal } from 'bootstrap'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import TableComponent from '@/components/TableComponent.vue'
import { useAttributeStore } from '@/store/attribute'

const attributeStore = useAttributeStore()

const attributes = computed(() => attributeStore.attributes)
const loading = computed(() => attributeStore.loading)
const isEdit = ref(false)
const attributeModalRef = ref<HTMLElement | null>(null)
let modalInstance: Modal | null = null

const form = reactive({
  id: null as number | null,
  name: '',
  values: [] as { id?: number; value: string }[],
})

onMounted(async () => {
  await attributeStore.fetchAttributes()
})

const columns = computed(() => [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
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
    values: attribute.values || [],
  })
  showModal()
}

function showModal() {
  nextTick(() => {
    if (!modalInstance && attributeModalRef.value) {
      modalInstance = new Modal(attributeModalRef.value)
    }
    modalInstance?.show()
  })
}

function closeModal() {
  modalInstance?.hide()
}

function addValue() {
  form.values.push({ value: '' })
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
</script>

<style scoped>
.modal-body {
  max-height: 80vh;
  overflow-y: auto;
}
</style>
