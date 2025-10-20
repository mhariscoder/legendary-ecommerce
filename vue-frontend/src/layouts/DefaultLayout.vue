<template>
  <div>
    <Navbar />
    <main class="container mt-4">
      <slot />
    </main>
  </div>
</template>

<script setup>
    import Navbar from '../components/common/Navbar.vue'
</script>