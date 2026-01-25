<template>
  <span v-if="html" class="vw-raw-slot" style="display: contents" v-html="html"></span>
</template>

<script setup lang="ts">
import { computed, inject } from 'vue'
import type { SlotHtmlMap } from '@/contracts'

const props = defineProps<{ name: string }>()

const injectedProps = inject<Record<string, any>>('props', {})
const slots = computed<SlotHtmlMap>(() => (injectedProps?.slots || {}) as SlotHtmlMap)
const html = computed(() => slots.value[props.name] || '')
</script>

