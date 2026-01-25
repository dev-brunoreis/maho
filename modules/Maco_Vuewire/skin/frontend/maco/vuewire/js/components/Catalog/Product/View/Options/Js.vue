<template>
  <span style="display:none" aria-hidden="true"></span>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'

onMounted(() => {
  if (window.Product && window.Product.OptionsDate) {
    window.dateOption = new window.Product.OptionsDate()
  }
})
</script>
