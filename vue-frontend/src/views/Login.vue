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
                <div class="card-header bg-primary text-center p-2">
                  <router-link
                    class="font-sans-serif fw-bolder fs-5 text-white text-decoration-none"
                    to="/"
                  >
                    falcon
                  </router-link>
                </div>

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
                        <div class="mb-3">
                            <label class="form-label" for="email">Email address</label>
                            <input
                                id="email"
                                v-model="email"
                                type="email"
                                class="form-control"
                                @blur="emailBlur"
                            />
                            <small v-if="emailError && emailMeta.touched" class="text-danger">
                                {{ emailError }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password</label>
                            </div>
                            <input
                                id="password"
                                v-model="password"
                                type="password"
                                class="form-control"
                                @blur="passwordBlur"
                            />
                            <small v-if="passwordError && passwordMeta.touched" class="text-danger">
                                {{ passwordError }}
                            </small>
                        </div>

                        <div class="row flex-between-center mb-3">
                            <div class="col-auto">
                                <div class="form-check mb-0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="rememberMe"
                                    v-model="remember"
                                />
                                <label class="form-check-label mb-0" for="rememberMe">Remember me</label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <router-link class="fs-10" to="/forgot-password">Forgot Password?</router-link>
                            </div>
                        </div>

                        <button class="btn btn-primary d-block w-100 mt-3" type="submit">
                            Log in
                        </button>
                    </form>

                    <div class="position-relative mt-4">
                        <hr />
                        <div class="divider-content-center text-muted small">or log in with</div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-sm-6"><a class="btn btn-outline-google-plus btn-sm d-block w-100" href="#"><span class="fab fa-google-plus-g me-2" data-fa-transform="grow-8"></span> google</a></div>
                        <div class="col-sm-6"><a class="btn btn-outline-facebook btn-sm d-block w-100" href="#"><span class="fab fa-facebook-square me-2" data-fa-transform="grow-8"></span> facebook</a></div>
                    </div>
                </div> <!-- /card-body -->
              </div> <!-- /card -->
            </div>
          </div>
        </div> <!-- /right column -->
      </div>
    </div>
  </main>
</template>

<script setup>
    import { ref } from 'vue'
    import { useForm, useField } from 'vee-validate'
    import * as yup from 'yup'
    import { useMainStore } from '@/store'

    // ✅ Dynamically import the image (works in Vite)
    const bgImage = new URL('@/assets/img/generic/14.jpg', import.meta.url).href

    // Store
    const store = useMainStore()
    const remember = ref(false)

    // ✅ Validation Schema
    const schema = yup.object({
    email: yup.string().email('Invalid email').required('Email is required'),
    password: yup.string().min(6, 'Password must be at least 6 characters').required('Password is required'),
    })

    // ✅ Form setup
    const { handleSubmit } = useForm({ validationSchema: schema })

    // ✅ Individual fields
    const {
    value: email,
    errorMessage: emailError,
    handleBlur: emailBlur,
    meta: emailMeta,
    } = useField('email')

    const {
    value: password,
    errorMessage: passwordError,
    handleBlur: passwordBlur,
    meta: passwordMeta,
    } = useField('password')

    // ✅ Submit handler
    const onSubmit = handleSubmit(async (values) => {
    await store.login({ ...values, remember: remember.value })
    })
</script>

<style scoped>
.bg-holder {
  width: 100%;
  height: 100%;
  background-size: cover;
  background-repeat: no-repeat;
}
</style>
