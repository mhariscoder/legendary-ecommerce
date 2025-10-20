<template><router-view /></template>

<script setup>
// no need to import HelloWorld or TheWelcome anymore
</script>

<style scoped>
  main {
    min-height: calc(100vh - 160px); /* adjust for navbar & footer */
    background-color: #f8f9fa;
  }

  .navbar-brand {
    font-size: 1.25rem;
  }

  footer {
    position: relative;
    bottom: 0;
    width: 100%;
  }
</style>