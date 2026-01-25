<template>
  <div class="product-options" id="product-options-wrapper">
    <slot name="children"></slot>
    <p v-if="hasRequiredOptions" class="required">{{ $t('* Required Fields') }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewOptionsWrapperPropsV1 } from '@/contracts/catalog/product/view/options/wrapper'

const props = defineProps<CatalogProductViewOptionsWrapperPropsV1>()
const hasRequiredOptions = computed(() => props.hasRequiredOptions || false)
</script>
