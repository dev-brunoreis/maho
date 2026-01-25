<template>
  <div>
    <slot name="product_type_data_extra"></slot>
  </div>
</template>

<script setup lang="ts">
import type { CatalogProductViewTypeDefaultPropsV1 } from '@/contracts/catalog/product/view/type/default'

defineProps<CatalogProductViewTypeDefaultPropsV1>()
</script>
