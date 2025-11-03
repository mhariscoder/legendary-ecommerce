<template>
  <div class="dashboard-container d-flex">
    <Sidebar />

    <div class="dashboard-content flex-grow-1 p-4">
      <h2 class="mb-4">Dashboard</h2>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <p class="mb-0">
            Welcome to your dashboard! You can add widgets, charts, or analytics here.
          </p>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5>Recent Activity</h5>
              <p>Display your recent actions here...</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5>Statistics</h5>
              <p>Charts and numbers go here...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Sidebar from '@/components/Sidebar.vue'
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background-color: #f8f9fa;
}

.dashboard-content {
  width: 100%;
  overflow-y: auto;
}

.card {
  border-radius: 0.75rem;
}
</style>