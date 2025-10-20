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

        <!-- RIGHT SIDE REGISTER FORM -->
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
                      <h3 class="fw-bold mb-0">Register</h3>
                    </div>
                    <div class="col-auto fs-10 text-600">
                      <span class="fw-semi-bold">Already have an account?</span>
                      <router-link to="/login"> Login </router-link>
                    </div>
                  </div>

                  <!-- ✅ REGISTER FORM -->
                  <form @submit.prevent="onSubmit">
                    <div class="mb-3">
                      <label class="form-label" for="name">Name</label>
                      <input
                        id="name"
                        v-model="name"
                        type="text"
                        class="form-control"
                        @blur="nameBlur"
                      />
                      <small v-if="nameError && nameMeta.touched" class="text-danger">
                        {{ nameError }}
                      </small>
                    </div>

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

                    <div class="row gx-2">
                      <div class="mb-3 col-sm-6">
                        <label class="form-label" for="password">Password</label>
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
                      <div class="mb-3 col-sm-6">
                        <label class="form-label" for="confirmPassword">Confirm Password</label>
                        <input
                          id="confirmPassword"
                          v-model="confirmPassword"
                          type="password"
                          class="form-control"
                          @blur="confirmPasswordBlur"
                        />
                        <small
                          v-if="confirmPasswordError && confirmPasswordMeta.touched"
                          class="text-danger"
                        >
                          {{ confirmPasswordError }}
                        </small>
                      </div>
                    </div>

                    <div class="form-check mb-3">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="agree"
                        v-model="agree"
                      />
                      <label class="form-check-label" for="agree">
                        I accept the
                        <a href="#!">terms</a> and
                        <a href="#!">privacy policy</a>
                      </label>
                      <small v-if="agreeError" class="text-danger d-block">{{ agreeError }}</small>
                    </div>

                    <button class="btn btn-primary d-block w-100 mt-3" type="submit">
                      Register
                    </button>
                  </form>

                  <div class="position-relative mt-4">
                    <hr />
                    <div class="divider-content-center text-muted small">
                      or register with
                    </div>
                  </div>

                  <div class="row g-2 mt-2">
                    <div class="col-sm-6">
                      <a
                        class="btn btn-outline-google-plus btn-sm d-block w-100"
                        href="#"
                      >
                        <span
                          class="fab fa-google-plus-g me-2"
                          data-fa-transform="grow-8"
                        ></span>
                        Google
                      </a>
                    </div>
                    <div class="col-sm-6">
                      <a
                        class="btn btn-outline-facebook btn-sm d-block w-100"
                        href="#"
                      >
                        <span
                          class="fab fa-facebook-square me-2"
                          data-fa-transform="grow-8"
                        ></span>
                        Facebook
                      </a>
                    </div>
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

    // ✅ Image path (Vite compatible)
    const bgImage = new URL('@/assets/img/generic/19.jpg', import.meta.url).href

    // ✅ Store
    const store = useMainStore()

    // ✅ Validation Schema
    const schema = yup.object({
    name: yup.string().required('Name is required'),
    email: yup.string().email('Invalid email').required('Email is required'),
    password: yup.string().min(6, 'Password must be at least 6 characters').required('Password is required'),
    confirmPassword: yup
        .string()
        .oneOf([yup.ref('password')], 'Passwords must match')
        .required('Confirm your password'),
    })

    // ✅ Form setup
    const { handleSubmit } = useForm({ validationSchema: schema })

    // ✅ Fields
    const { value: name, errorMessage: nameError, handleBlur: nameBlur, meta: nameMeta } = useField('name')
    const { value: email, errorMessage: emailError, handleBlur: emailBlur, meta: emailMeta } = useField('email')
    const { value: password, errorMessage: passwordError, handleBlur: passwordBlur, meta: passwordMeta } = useField('password')
    const { value: confirmPassword, errorMessage: confirmPasswordError, handleBlur: confirmPasswordBlur, meta: confirmPasswordMeta } = useField('confirmPassword')

    // ✅ Checkbox
    const agree = ref(false)
    const agreeError = ref('')

    // ✅ Submit
    const onSubmit = handleSubmit(async (values) => {
        if (!agree.value) {
            agreeError.value = 'You must accept the terms and privacy policy.'
            return
        } else {
            agreeError.value = ''
        }

        await store.register(values)
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