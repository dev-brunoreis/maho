<template>
  <div v-if="links?.length" class="links">
    <div v-if="title" class="block-title">{{ $t(title) }}</div>
    <ul :id="name || undefined">
      <template v-for="(link, index) in links" :key="index">
        <li
          v-if="link.type === 'link'"
          :class="{ first: link.isFirst, last: link.isLast }"
          v-bind="link.liParams ? parseParamString(link.liParams) : {}"
        >
          {{ link.beforeText }}
          <a
            :href="link.url"
            :title="link.title"
            v-bind="link.aParams ? parseParamString(link.aParams) : {}"
          >
            {{ link.label }}
          </a>
          {{ link.afterText }}
        </li>
        <li v-else-if="link.type === 'html'" v-html="link.html"></li>
      </template>
    </ul>
  </div>
</template>

<script setup lang="ts">
import type { PageTemplateLinksPropsV1 } from '@/contracts/page/template_links'

defineProps<PageTemplateLinksPropsV1>()

function parseParamString(params: string): Record<string, string> {
  if (!params || typeof params !== 'string') return {}
  const attrs: Record<string, string> = {}
  const regex = /\s+(\w+)=["']([^"']*)["']/g
  let m
  while ((m = regex.exec(params)) !== null) {
    attrs[m[1]] = m[2]
  }
  return attrs
}
</script>
