<template>

<div class="flex justify-between items-center">
  <a :href="props.baseUrl ?? '/'">
    <slot class="flex" name="logo" />
  </a>
  <slot class="flex" name="topSearch"></slot>
  
  <slot name="topLinks"></slot>
</div>

    <div class="flex justify-between items-center">
      <slot name="before"></slot>
      <slot name="topMenu"></slot>
      <slot name="after"></slot>
    </div>
</template>

<script setup lang="ts">
import type { PageHtmlHeaderPropsV1 } from '@/contracts/page/html/header'

const props = defineProps<PageHtmlHeaderPropsV1>()
</script>
