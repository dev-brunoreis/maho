<template>
  <div v-if="displayProductStockStatus">
    <p v-if="isAvailable" class="availability in-stock">
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
import type { CatalogProductViewTypeAvailabilityDefaultPropsV1 } from '@/contracts/catalog/product/view/type/availability_default'

const props = defineProps<CatalogProductViewTypeAvailabilityDefaultPropsV1>()
const isAvailable = computed(() => props.isAvailable || false)
const displayProductStockStatus = computed(() => props.displayProductStockStatus || false)
</script>
