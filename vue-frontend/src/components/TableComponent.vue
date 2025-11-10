<template>
  <div>
    <!-- Actions -->
    <div class="d-flex align-items-center justify-content-between my-3">
      <!-- Left: Search -->
      <div class="d-flex align-items-center">
        <input
          type="text"
          class="form-control form-control-sm"
          placeholder="Search..."
          v-model="searchQuery"
        />
        <button class="btn btn-sm btn-primary ms-2" @click="applySearch">Search</button>
        <button class="btn btn-sm btn-secondary ms-1" @click="clearSearch">Clear</button>
      </div>

      <!-- Right: Bulk & Create -->
      <div class="d-flex align-items-center">
        <!-- <div class="me-2">
          <select class="form-select form-select-sm" v-model="bulkAction">
            <option value="">Bulk actions</option>
            <option value="Delete">Delete</option>
            <option value="Archive">Archive</option>
          </select>
        </div>
        <button class="btn btn-sm btn-danger me-2" @click="emitBulk">Apply</button> -->

        <button class="btn btn-sm btn-success" @click="$emit('create')">
          <span class="fas fa-plus me-1"></span> New
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive scrollbar">
      <table class="table table-bordered table-striped fs-10 mb-0">
        <thead class="bg-200">
          <tr>
            <th v-for="col in columns" :key="col.key">{{ col.label }}</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loading -->
          <tr v-if="loading">
            <td :colspan="columns.length" class="text-center py-3">Loading...</td>
          </tr>

          <!-- Error -->
          <tr v-else-if="error">
            <td :colspan="columns.length" class="text-danger text-center py-3">{{ error }}</td>
          </tr>

          <!-- Empty -->
          <tr v-else-if="!displayData.length">
            <td :colspan="columns.length" class="text-center text-muted py-3">No data available.</td>
          </tr>

          <!-- Data rows -->
          <tr v-else v-for="(row, index) in displayData" :key="index">
            <td v-for="col in columns" :key="col.key" :style="{width: (col.key === 'actions') ? '10%' : 'auto'}">
              <template v-if="col.key === 'actions'">
                <button class="btn btn-sm btn-transparent text-warning" @click="emit('edit', row)">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-transparent text-danger" @click="emit('remove', row)">
                  <i class="fas fa-trash"></i>
                </button>
              </template>
              <template v-else>
                {{ row[col.key] }}
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3 mb-1">
      <button
        class="btn btn-sm btn-falcon-default me-1"
        :disabled="!hasPrevPage"
        @click="goToPage(currentPage - 1)"
      >
        <span class="fas fa-chevron-left"></span>
      </button>

      <ul class="pagination mb-0">
        <li v-for="page in totalPages" :key="page">
          <button
            class="btn btn-sm btn-falcon-default ms-1"
            :class="{ active: currentPage === page }"
            @click="goToPage(page)"
          >
            {{ page }}
          </button>
        </li>
      </ul>

      <button
        class="btn btn-sm btn-falcon-default ms-1"
        :disabled="!hasNextPage"
        @click="goToPage(currentPage + 1)"
      >
        <span class="fas fa-chevron-right"></span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'

interface Column {
  key: string
  label: string
}

interface Pagination {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

interface Props {
  columns: Column[]
  data: Record<string, any>[]
  perPage?: number
  pagination?: Pagination | null
  loading?: boolean
  error?: string | null
}

const props = defineProps<Props>()
const emit = defineEmits(['create', 'bulk', 'page-change', 'search', 'edit', 'remove'])

const bulkAction = ref('')
const searchQuery = ref('')
const localPage = ref(1)
const perPage = computed(() => props.perPage ?? 5)

const isServerPaginated = computed(() => !!props.pagination)
const currentPage = computed({
  get: () => (isServerPaginated.value ? props.pagination?.current_page || 1 : localPage.value),
  set: val => {
    if (!isServerPaginated.value) localPage.value = val
  },
})
const totalPages = computed(() =>
  isServerPaginated.value
    ? props.pagination?.last_page || 1
    : Math.ceil(props.data.length / perPage.value)
)
const displayData = computed(() => {
  let filtered = props.data
  if (searchQuery.value) {
    filtered = filtered.filter(row =>
      Object.values(row).some(val =>
        String(val).toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    )
  }
  if (!isServerPaginated.value) {
    const start = (localPage.value - 1) * perPage.value
    filtered = filtered.slice(start, start + perPage.value)
  }
  return filtered
})

const hasPrevPage = computed(() => currentPage.value > 1)
const hasNextPage = computed(() => currentPage.value < totalPages.value)

function goToPage(page: number) {
  if (page < 1 || page > totalPages.value) return
  if (isServerPaginated.value) emit('page-change', page)
  else localPage.value = page
}

function emitBulk() {
  if (bulkAction.value) emit('bulk', bulkAction.value)
}

function applySearch() {
  emit('search', searchQuery.value)
}

function clearSearch() {
  searchQuery.value = ''
  emit('search', '')
}
</script>

<style scoped>
.table {
  border-radius: 0.5rem;
}
.pagination .active {
  background-color: #0d6efd;
  color: #fff;
}
</style>
