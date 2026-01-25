<template>
  <div v-if="displayProductStockStatus">
    <p v-if="isAvailable && hasAssociatedProducts" class="availability in-stock">
      <span class="label">{{ $t('Availability:') }}</span>
      <span class="value">{{ $t('In stock') }}</span>
    </p>
    <p v-else class="availability out-of-stock">
      <span class="label">{{ $t('Availability:') }}</span>
      <span class="value">{{ $t('Out of stock') }}</span>
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewTypeAvailabilityGroupedPropsV1 } from '@/contracts/catalog/product/view/type/availability_grouped'

const props = defineProps<CatalogProductViewTypeAvailabilityGroupedPropsV1>()
const isAvailable = computed(() => props.isAvailable || false)
const hasAssociatedProducts = computed(() => props.hasAssociatedProducts || false)
const displayProductStockStatus = computed(() => props.displayProductStockStatus || false)
</script>
