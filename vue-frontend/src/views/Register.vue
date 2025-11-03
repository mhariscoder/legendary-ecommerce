<template>
  <main class="main" id="top">
    <div class="container-fluid">
      <div class="row min-vh-100 bg-100">
        <!-- LEFT SIDE IMAGE -->
        <div class="col-6 d-none d-lg-block position-relative">
          <div
            class="bg-holder"
            :style="{ backgroundImage: `url(${bgImage})` }"
          ></div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-sm-10 col-md-6 align-self-center mx-auto py-5">
          <div class="row justify-content-center g-0">
            <div class="col-lg-9 col-xl-8 col-xxl-6">
              <div class="card shadow-sm">

                <div class="card-body p-4">
                  <div class="row flex-between-center mb-3">
                    <div class="col-auto">
                      <h3 class="fw-bold mb-0">Register</h3>
                    </div>
                    <div class="col-auto fs-10 text-600">
                      <span class="fw-semi-bold">Already have an account?</span>
                      <router-link to="/login"> Login </router-link>
                    </div>
                  </div>

                  <form @submit.prevent="onSubmit">
                    <FormInput id="name" label="Name" v-model="form.name" :error="errors.name" />
                    <FormInput id="email" label="Email" type="email" v-model="form.email" :error="errors.email" />
                    <FormInput id="password" label="Password" type="password" v-model="form.password" :error="errors.password" />
                    <FormInput id="password_confirmation" label="Confirm Password" type="password" v-model="form.password_confirmation" :error="errors.password_confirmation" />

                    <div class="form-check mb-3">
                      <input class="form-check-input" type="checkbox" id="agree" v-model="agree" />
                      <label class="form-check-label" for="agree"> 
                        I agree to the terms and privacy policy
                      </label><br/>
                      <small v-if="agreeError" class="text-danger">{{ agreeError }}</small>
                    </div>

                    <button class="btn btn-primary w-100 mt-3" type="submit" :disabled="loading">
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                      {{ loading ? 'Registering...' : 'Register' }}
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import * as yup from 'yup'
import FormInput from '@/components/common/FormInput.vue'
import { useMainStore } from '@/store'
import { validate } from '@/utils/validate'
import type { RegisterForm } from '@/types/auth'

const bgImage = new URL('@/assets/img/generic/19.jpg', import.meta.url).href
const store = useMainStore()
const loading = ref(false)

const form = reactive<RegisterForm>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const errors = reactive<Partial<Record<keyof RegisterForm, string>>>({})
const agree = ref(false)
const agreeError = ref('')

const schema = yup.object({
  name: yup.string().required('Name is required'),
  email: yup.string().email('Invalid email').required('Email is required'),
  password: yup.string().min(6, 'Password must be at least 6 characters').required('Password is required'),
  password_confirmation: yup
    .string()
    .required('Confirm Password is required')
    .oneOf([yup.ref('password')], 'Passwords must match'),
})

const onSubmit = async () => {
  const { valid, fieldErrors } = await validate(schema, form)
  Object.assign(errors, fieldErrors)

  if (!valid) return
  if (!agree.value) {
    agreeError.value = 'Please accept the terms.'
    return
  }

  loading.value = true
  agreeError.value = ''
  try {
    await store.register(form)
    alert('✅ Registered successfully!')
  } catch (err: any) {
    alert(err.message)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-holder {
  height: 100%;
  background-size: cover;
  background-position: center;
}
</style>
