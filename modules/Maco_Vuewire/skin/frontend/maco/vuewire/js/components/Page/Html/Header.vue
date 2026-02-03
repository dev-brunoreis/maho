<template>
    <div class="flex justify-between items-center">
      <slot name="before"></slot>
      <slot name="logo"></slot>
      <slot name="topLinks"></slot>
      <slot name="topMenu"></slot>
      <slot name="after"></slot>
    </div>
</template>

<script setup lang="ts">
import type { PageHtmlHeaderPropsV1 } from '@/contracts/page/html/header'

defineProps<PageHtmlHeaderPropsV1>()
</script>
