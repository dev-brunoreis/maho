<template>
  <nav v-if="menuHtml" class="nav-container" role="navigation" :aria-label="$t('Main navigation')">
    <div class="nav-menu" v-html="menuHtml"></div>
  </nav>
</template>

<script setup lang="ts">
import type { PageHtmlTopmenuPropsV1 } from '@/contracts/page/html/topmenu'

defineProps<PageHtmlTopmenuPropsV1>()
</script>
