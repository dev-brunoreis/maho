<template>
  <dt>
    <label>{{ option.title }}</label>
  </dt>
</template>

<script setup lang="ts">
import type { CatalogProductViewOptionsTypeDefaultPropsV1 } from '@/contracts/catalog/product/view/options/type/default'

defineProps<CatalogProductViewOptionsTypeDefaultPropsV1>()
</script>
