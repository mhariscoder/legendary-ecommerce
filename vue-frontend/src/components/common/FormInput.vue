<template>
  <div class="mb-3">
    <label :for="id" class="form-label">{{ label }}</label>
    <input
      :id="id"
      :type="type"
      class="form-control"
      :placeholder="placeholder"
      v-model="model"
      @blur="$emit('blur')"
    />
    <small v-if="error" class="text-danger">{{ error }}</small>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  id: string
  label: string
  modelValue: string
  type?: string
  placeholder?: string
  error?: string
}>()

const emits = defineEmits(['update:modelValue', 'blur'])

const model = defineModel<string>('modelValue')
</script>