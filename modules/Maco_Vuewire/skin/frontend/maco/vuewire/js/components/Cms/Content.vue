<template>
  <div v-html="pageContent"></div>
</template>

<script setup lang="ts">
import type { CmsContentPropsV1 } from '@/contracts/cms/content'

defineProps<CmsContentPropsV1>()
</script>
