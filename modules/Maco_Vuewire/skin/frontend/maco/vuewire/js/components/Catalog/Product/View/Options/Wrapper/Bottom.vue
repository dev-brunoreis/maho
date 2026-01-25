<template>
  <div class="product-options-bottom">
    <slot name="children"></slot>
  </div>
</template>

<script setup lang="ts">
import type { CatalogProductViewOptionsWrapperBottomPropsV1 } from '@/contracts/catalog/product/view/options/wrapper_bottom'

defineProps<CatalogProductViewOptionsWrapperBottomPropsV1>()
</script>
