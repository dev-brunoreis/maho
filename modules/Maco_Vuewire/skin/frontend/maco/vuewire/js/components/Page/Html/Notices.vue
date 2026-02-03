<template>
  <aside
    v-if="showDemoNotice && demoNoticeContent"
    class="global-site-notice demo-notice"
    role="complementary"
    :aria-label="$t('Store Notice')"
  >
    <div class="notice-inner" v-html="demoNoticeContent"></div>
  </aside>
</template>

<script setup lang="ts">
import type { PageHtmlNoticesPropsV1 } from '@/contracts/page/html/notices'

defineProps<PageHtmlNoticesPropsV1>()
</script>
