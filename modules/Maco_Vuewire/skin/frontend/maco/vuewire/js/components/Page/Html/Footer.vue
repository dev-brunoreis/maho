<template>
  <footer class="footer-container" role="contentinfo">
    <div class="footer">
      <slot name="content"></slot>
      <address v-if="copyright">{{ copyright }}</address>
    </div>
  </footer>
</template>

<script setup lang="ts">
import type { PageHtmlFooterPropsV1 } from '@/contracts/page/html/footer'

defineProps<PageHtmlFooterPropsV1>()
</script>
