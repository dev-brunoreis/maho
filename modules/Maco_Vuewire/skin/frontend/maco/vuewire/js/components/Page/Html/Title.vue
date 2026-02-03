<template>
  <h1 v-if="title" class="page-title">{{ title }}</h1>
</template>

<script setup lang="ts">
import type { PageHtmlTitlePropsV1 } from '@/contracts/page/html/title'

defineProps<PageHtmlTitlePropsV1>()
</script>
