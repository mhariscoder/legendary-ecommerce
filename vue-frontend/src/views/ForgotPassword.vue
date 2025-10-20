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
                <div class="card-header bg-primary text-center p-2">
                  <router-link
                    class="font-sans-serif fw-bolder fs-5 text-white text-decoration-none"
                    to="/"
                  >
                    falcon
                  </router-link>
                </div>

                <div class="card-body p-4 text-center">
                  <h4 class="mb-1">Forgot your password?</h4>
                  <small>Enter your email and we’ll send you a reset link.</small>

                  <form class="mt-4" @submit.prevent="onSubmit">
                    <div class="mb-3">
                      <input
                        type="email"
                        class="form-control"
                        placeholder="Email address"
                        v-model="email"
                        @blur="emailBlur"
                      />
                      <small v-if="emailError && emailMeta.touched" class="text-danger">
                        {{ emailError }}
                      </small>
                    </div>

                    <button class="btn btn-primary d-block w-100 mt-3" type="submit">
                      Send reset link
                    </button>
                  </form>

                  <router-link class="fs-10 text-600 d-block mt-3" to="/login">
                    Back to login <span class="d-inline-block ms-1">&rarr;</span>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- /right column -->
      </div>
    </div>
  </main>
</template>

<script setup>
    import { useForm, useField } from 'vee-validate'
    import * as yup from 'yup'
    import { useMainStore } from '@/store'

    // ✅ Background image (Vite-compatible)
    const bgImage = new URL('@/assets/img/generic/17.jpg', import.meta.url).href

    // ✅ Store
    const store = useMainStore()

    // ✅ Validation schema
    const schema = yup.object({
    email: yup.string().email('Invalid email').required('Email is required'),
    })

    // ✅ Form setup
    const { handleSubmit } = useForm({ validationSchema: schema })
    const {
    value: email,
    errorMessage: emailError,
    handleBlur: emailBlur,
    meta: emailMeta,
    } = useField('email')

    // ✅ Submit handler
    const onSubmit = handleSubmit(async (values) => {
    await store.forgotPassword(values.email)
    })
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
