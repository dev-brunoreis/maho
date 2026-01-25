<template>
  <div>
    <slot name="children"></slot>
  </div>
</template>

<script setup lang="ts">
import type { CatalogProductViewAdditionalPropsV1 } from '@/contracts/catalog/product/view/additional'

defineProps<CatalogProductViewAdditionalPropsV1>()
</script>
