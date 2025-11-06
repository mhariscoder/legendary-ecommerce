<template>
    <main class="main" id="top">
        <div class="container-fluid" data-layout="container">
            <Sidebar />

            <div class="content">
              <Topbar /> 
              
              <slot />
            </div>
        </div>
    </main>
</template>

<script setup lang="ts">
import Sidebar from '@/components/Sidebar.vue'
import Topbar from '@/components/Topbar.vue'
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.dashboard-content {
  flex: 1;
  padding: 1.5rem;
  overflow-x: hidden;
}
</style>