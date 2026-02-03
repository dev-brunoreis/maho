<template>
  <div class="breadcrumbs" role="navigation" :aria-label="$t('Breadcrumbs')">
    <slot name="before"></slot>
    <ul v-if="crumbs?.length">
      <li
        v-for="(crumb, index) in crumbs"
        :key="index"
        :class="{ last: index === crumbs.length - 1 }"
      >
        <template v-if="crumb.link">
          <a :href="crumb.link">{{ crumb.label }}</a>
        </template>
        <strong v-else class="current">{{ crumb.label }}</strong>
      </li>
    </ul>
    <slot name="after"></slot>
  </div>
</template>

<script setup lang="ts">
import type { PageHtmlBreadcrumbsPropsV1 } from '@/contracts/page/html/breadcrumbs'

defineProps<PageHtmlBreadcrumbsPropsV1>()
</script>
