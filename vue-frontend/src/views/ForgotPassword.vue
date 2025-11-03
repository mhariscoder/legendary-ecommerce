<template>
  <main class="main" id="top">
    <div class="container-fluid">
      <div class="row min-vh-100 bg-100">
        <!-- LEFT SIDE IMAGE -->
        <div class="col-6 d-none d-lg-block position-relative">
          <div
            class="bg-holder overlay"
            :style="{ backgroundImage: `url(${bgImage})`, backgroundPosition: '50% 76%', backgroundSize: 'cover' }"
          ></div>
        </div>

        <!-- RIGHT SIDE FORM -->
        <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
          <div class="row justify-content-center g-0">
            <div class="col-lg-9 col-xl-8 col-xxl-6">
              <div class="card shadow-sm">

                <div class="card-body p-4 text-center">
                  <h4 class="mb-1">Forgot your password?</h4>
                  <small>Enter your email and we’ll send you a reset link.</small>

                  <form class="mt-4" @submit.prevent="onSubmit">
                    <FormInput
                      id="email"
                      label="Email address"
                      type="email"
                      v-model="form.email"
                      :error="errors.email"
                    />

                    <button class="btn btn-primary d-block w-100 mt-3" type="submit" :disabled="loading">
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                      {{ loading ? 'Sending...' : 'Send reset link' }}
                    </button>
                  </form>

                  <router-link class="fs-10 text-600 d-block mt-3" to="/login">
                    Back to login <span class="d-inline-block ms-1">&rarr;</span>
                  </router-link>
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

const bgImage = new URL('@/assets/img/generic/17.jpg', import.meta.url).href
const store = useMainStore()
const loading = ref(false)

const form = reactive({
  email: '',
})

const errors = reactive<{ email?: string }>({})

const schema = yup.object({
  email: yup.string().email('Invalid email').required('Email is required'),
})

const onSubmit = async () => {
  const { valid, fieldErrors } = await validate(schema, form)
  Object.assign(errors, fieldErrors)

  if (!valid) return

  loading.value = true
  try {
    await store.forgotPassword(form.email)
    alert('✅ Password reset link sent to your email!')
  } catch (err: any) {
    alert(err.message || 'Something went wrong')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-holder {
  width: 100%;
  height: 100%;
  background-size: cover;
  background-repeat: no-repeat;
}
.overlay {
  position: relative;
}
</style>
