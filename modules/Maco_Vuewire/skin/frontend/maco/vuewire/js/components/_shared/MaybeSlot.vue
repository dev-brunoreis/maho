<template>
  <slot v-if="hasVueSlot" :name="name"></slot>
  <RawHtmlSlot v-else :name="name" />
</template>

<script setup lang="ts">
import { computed, useSlots } from 'vue'
import RawHtmlSlot from './RawHtmlSlot.vue'

const props = defineProps<{ name: string }>()
const slots = useSlots()

const hasVueSlot = computed(() => Boolean(slots[props.name]))
const name = computed(() => props.name)
</script>

