<template>
  <div v-if="description" class="std" v-html="description"></div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewDescriptionPropsV1 } from '@/contracts/catalog/product/view/description'

const props = defineProps<CatalogProductViewDescriptionPropsV1>()
const description = computed(() => props.description || '')
</script>
