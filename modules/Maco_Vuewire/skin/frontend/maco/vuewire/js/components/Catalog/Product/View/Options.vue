<template>
  <div v-if="options && options.length > 0">
    <dl>
      <slot v-for="(option, index) in options" :key="index" :name="'option-' + option.id">
        <!-- Options will be rendered by child components -->
      </slot>
    </dl>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import type { CatalogProductViewOptionsPropsV1 } from '@/contracts/catalog/product/view/options'

const props = defineProps<CatalogProductViewOptionsPropsV1>()
const options = computed(() => props.options || [])
const jsonConfig = computed(() => props.jsonConfig || null)

onMounted(() => {
  ; (window as any).validateOptionsCallback = (elmId: string, result: string) => {
    const container = document.getElementById(elmId)?.closest('ul.options-list')
    if (!container) return
    if (result === 'failed') {
      container.classList.remove('validation-passed')
      container.classList.add('validation-failed')
    } else {
      container.classList.remove('validation-failed')
      container.classList.add('validation-passed')
    }
  }

  if (jsonConfig.value && (window as any).Product && (window as any).Product.Options) {
    ; (window as any).opConfig = new (window as any).Product.Options(jsonConfig.value)
  }
})
</script>
