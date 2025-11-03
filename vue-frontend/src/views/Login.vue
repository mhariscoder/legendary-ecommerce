<template>
  <main class="main" id="top">
    <div class="container-fluid">
      <div class="row min-vh-100 bg-100">
        <!-- LEFT SIDE IMAGE -->
        <div class="col-6 d-none d-lg-block position-relative">
          <div
            class="bg-holder"
            :style="{ backgroundImage: `url(${bgImage})`, backgroundPosition: '50% 20%', backgroundSize: 'cover' }"
          ></div>
        </div>

        <!-- RIGHT SIDE LOGIN FORM -->
        <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
          <div class="row justify-content-center g-0">
            <div class="col-lg-9 col-xl-8 col-xxl-6">
              <div class="card shadow-sm">
                
                <div class="card-body p-4">
                  <div class="row flex-between-center mb-3">
                    <div class="col-auto">
                      <h3 class="fw-bold mb-0">Login</h3>
                    </div>
                    <div class="col-auto fs-10 text-600">
                      <span class="fw-semi-bold">New User?</span>
                      <router-link to="/register"> Create account </router-link>
                    </div>
                  </div>

                  <!-- ✅ LOGIN FORM -->
                  <form @submit.prevent="onSubmit">
                    <FormInput
                      id="email"
                      label="Email address"
                      type="email"
                      v-model="form.email"
                      :error="errors.email"
                    />
                    <FormInput
                      id="password"
                      label="Password"
                      type="password"
                      v-model="form.password"
                      :error="errors.password"
                    />

                    <!-- REMEMBER + FORGOT -->
                    <div class="row flex-between-center mb-3">
                      <div class="col-auto">
                        <div class="form-check mb-0">
                          <input
                            class="form-check-input"
                            type="checkbox"
                            id="rememberMe"
                            v-model="form.remember"
                          />
                          <label class="form-check-label mb-0" for="rememberMe">
                            Remember me
                          </label>
                        </div>
                      </div>
                      <div class="col-auto">
                        <router-link class="fs-10" to="/forgot-password">
                          Forgot Password?
                        </router-link>
                      </div>
                    </div>

                    <!-- SUBMIT -->
                    <button
                      class="btn btn-primary d-block w-100 mt-3"
                      type="submit"
                      :disabled="loading"
                    >
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                      {{ loading ? 'Logging in...' : 'Log in' }}
                    </button>
                  </form>

                </div> <!-- /card-body -->
              </div> <!-- /card -->
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

const bgImage = new URL('@/assets/img/generic/14.jpg', import.meta.url).href
const store = useMainStore()
const loading = ref(false)

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const errors = reactive<Partial<Record<'email' | 'password', string>>>({})

const schema = yup.object({
  email: yup.string().email('Please enter a valid email').required('Email is required'),
  password: yup.string().min(6, 'Password must be at least 6 characters').required('Password is required'),
})

const onSubmit = async () => {
  const { valid, fieldErrors } = await validate(schema, form)
  Object.assign(errors, fieldErrors)
  if (!valid) return

  loading.value = true
  try {
    await store.login({
      email: form.email,
      password: form.password,
      remember: form.remember,
    })
    alert('✅ Logged in successfully!')
  } catch (err: any) {
    alert(err.message)
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
</style>
