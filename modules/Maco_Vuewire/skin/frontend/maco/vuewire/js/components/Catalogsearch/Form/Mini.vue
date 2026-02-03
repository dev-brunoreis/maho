<template>
  <form
    id="search_mini_form"
    role="search"
    class="search-form"
    :action="resultUrl"
    method="get"
  >
    <div class="input-box">
      <label for="search">{{ $t('Search:') }}</label>
      <input
        id="search"
        v-model="query"
        type="search"
        :name="queryParamName"
        :maxlength="maxQueryLength"
        class="input-text required-entry"
        :placeholder="placeholder"
      />
      <button
        type="submit"
        class="button search-button"
        :title="searchButtonTitle"
      >
        <slot name="searchButtonContent"></slot>
      </button>
    </div>
    <slot name="after"></slot>
  </form>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import type { CatalogsearchFormMiniPropsV1 } from '@/contracts/catalogsearch/form_mini'

const props = defineProps<CatalogsearchFormMiniPropsV1>()
const query = ref(props.queryText)

onMounted(() => {
  if (
    typeof window !== 'undefined' &&
    (window as unknown as { Varien?: { searchForm?: unknown } }).Varien?.searchForm &&
    props.suggestUrl
  ) {
    const Varien = (window as unknown as { Varien: { searchForm: new (a: string, b: string, c: string) => { initAutocomplete: (url: string, containerId: string) => void } } }).Varien
    const form = document.getElementById('search_mini_form')
    const container = document.getElementById('search_autocomplete')
    if (form && container) {
      const searchForm = new Varien.searchForm('search_mini_form', 'search', '')
      searchForm.initAutocomplete(props.suggestUrl, 'search_autocomplete')
    }
  }
})
</script>
