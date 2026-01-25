<template>
  <div v-html="priceHtml"></div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewPriceClonePropsV1 } from '@/contracts/catalog/product/view/price_clone'

const props = defineProps<CatalogProductViewPriceClonePropsV1>()
const priceHtml = computed(() => props.priceHtml || '')
</script>
