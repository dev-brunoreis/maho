<template>
  <div v-if="additionalData && additionalData.length > 0">
    <table class="data-table" id="product-attribute-specs-table">
      <caption>{{ $t('Additional Information') }}</caption>
      <col width="25%" />
      <col />
      <tbody>
        <tr v-for="(data, index) in additionalData" :key="index">
          <th class="label">{{ $t(data.label) }}</th>
          <td class="data" v-html="data.value"></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewAttributesPropsV1 } from '@/contracts/catalog/product/view/attributes'

const props = defineProps<CatalogProductViewAttributesPropsV1>()
const additionalData = computed(() => props.additionalData || [])
</script>
