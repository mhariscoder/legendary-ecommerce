<template>
  <nav ref="navbarRef" class="navbar navbar-light navbar-vertical navbar-expand-xl navbar-vibrant">
    <div class="d-flex align-items-center">
      <div class="toggle-icon-wrapper">
        <button
          class="btn navbar-toggler-humburger-icon navbar-vertical-toggle"
          data-bs-toggle="tooltip"
          data-bs-placement="left"
          title="Toggle Navigation"
        >
          <span class="navbar-toggle-icon">
            <span class="toggle-line"></span>
          </span>
        </button>
      </div>

      <RouterLink class="navbar-brand" to="/">
        <div class="d-flex align-items-center py-3">
          <img
            class="me-2"
            :src="falconLogo"
            alt="Falcon logo"
            width="40"
          />
          <span class="font-sans-serif text-primary">falcon</span>
        </div>
      </RouterLink>
    </div>

    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
      <div class="navbar-vertical-content scrollbar">
        <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">

          <li class="nav-item">
            <a
              class="nav-link dropdown-indicator"
              href="#e-commerce"
              role="button"
              data-bs-toggle="collapse"
              aria-expanded="false"
              aria-controls="e-commerce"
            >
              <div class="d-flex align-items-center">
                <span class="nav-link-icon"><span class="fas fa-shopping-cart"></span></span>
                <span class="nav-link-text ps-1">E-Commerce</span>
              </div>
            </a>

            <ul class="nav collapse" id="e-commerce">

              <li class="nav-item">
                <a
                  class="nav-link dropdown-indicator"
                  href="#product"
                  data-bs-toggle="collapse"
                  aria-expanded="false"
                  aria-controls="product"
                >
                  <div class="d-flex align-items-center">
                    <span class="nav-link-text ps-1">Product</span>
                  </div>
                </a>

                <ul class="nav collapse" id="product">
                  <li class="nav-item" v-for="item in productItems" :key="item.path">
                    <RouterLink class="nav-link" :to="item.path">
                      <div class="d-flex align-items-center">
                        <span class="nav-link-text ps-1">{{ item.name }}</span>
                      </div>
                    </RouterLink>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <a
                  class="nav-link dropdown-indicator"
                  href="#orders"
                  data-bs-toggle="collapse"
                  aria-expanded="false"
                  aria-controls="orders"
                >
                  <div class="d-flex align-items-center">
                    <span class="nav-link-text ps-1">Orders</span>
                  </div>
                </a>

                <ul class="nav collapse" id="orders">
                  <li class="nav-item" v-for="item in orderItems" :key="item.path">
                    <RouterLink class="nav-link" :to="item.path">
                      <div class="d-flex align-items-center">
                        <span class="nav-link-text ps-1">{{ item.name }}</span>
                      </div>
                    </RouterLink>
                  </li>
                </ul>
              </li>

              <!-- Other Pages -->
              <li class="nav-item" v-for="item in ecommerceItems" :key="item.path">
                <RouterLink class="nav-link" :to="item.path">
                  <div class="d-flex align-items-center">
                    <span class="nav-link-text ps-1">{{ item.name }}</span>
                  </div>
                </RouterLink>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const navbarRef = ref<HTMLElement | null>(null)
const falconLogo = new URL('@/assets/img/icons/spot-illustrations/falcon.png', import.meta.url).href

interface NavItem {
  name: string
  path: string
}

const productItems: NavItem[] = [
  { name: 'Product List', path: '/ecommerce/product-list' },
  { name: 'Product Grid', path: '/ecommerce/product-grid' },
  { name: 'Product Details', path: '/ecommerce/product-details' },
  { name: 'Add Product', path: '/ecommerce/add-product' },
]

const orderItems: NavItem[] = [
  { name: 'Order List', path: '/ecommerce/order-list' },
  { name: 'Order Details', path: '/ecommerce/order-details' },
]

const ecommerceItems: NavItem[] = [
  { name: 'Customers', path: '/ecommerce/customers' },
  { name: 'Customer Details', path: '/ecommerce/customer-details' },
  { name: 'Shopping Cart', path: '/ecommerce/shopping-cart' },
  { name: 'Checkout', path: '/ecommerce/checkout' },
  { name: 'Billing', path: '/ecommerce/billing' },
  { name: 'Invoice', path: '/ecommerce/invoice' },
]

onMounted(() => {
  const navbarStyle = localStorage.getItem('navbarStyle')
  if (navbarStyle && navbarStyle !== 'transparent' && navbarRef.value) {
    navbarRef.value.classList.add(`navbar-${navbarStyle}`)
  }
})
</script>

<style scoped>
.navbar-vertical {
  transition: all 0.3s ease-in-out;
}
.toggle-icon-wrapper {
  margin-right: 1rem;
}
.scrollbar {
  max-height: 90vh;
  overflow-y: auto;
}
.nav-link.active {
  font-weight: 600;
  color: var(--bs-primary) !important;
}
</style>
