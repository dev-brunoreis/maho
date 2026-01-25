<template>
  <dt>
    <label :class="{ required: option.isRequired }" :for="'select_' + option.id">
      {{ option.title }}
    </label>
  </dt>
  <dd>
    <div class="input-box">
      <div v-html="valuesHtml"></div>
      <span v-if="option.isRequired && (option.type === 'radio' || option.type === 'checkbox')"
        :id="'options-' + option.id + '-container'"></span>
    </div>
  </dd>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewOptionsTypeSelectPropsV1 } from '@/contracts/catalog/product/view/options/type/select'

const props = defineProps<CatalogProductViewOptionsTypeSelectPropsV1>()
const option = computed(() => props.option)
const valuesHtml = computed(() => props.valuesHtml || '')
</script>
