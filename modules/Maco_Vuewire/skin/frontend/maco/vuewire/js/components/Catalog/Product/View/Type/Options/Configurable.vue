<template>
  <div v-if="isSaleable && attributes && attributes.length > 0">
    <dl>
      <template v-for="attribute in attributes" :key="attribute.id">
        <dt><label class="required">{{ attribute.label }}</label></dt>
        <dd>
          <div class="input-box">
            <select :name="'super_attribute[' + attribute.id + ']'" :id="'attribute' + attribute.id"
              class="required-entry super-attribute-select">
              <option>{{ $t('Choose an Option...') }}</option>
            </select>
          </div>
        </dd>
      </template>
    </dl>
    <slot name="after"></slot>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import type { CatalogProductViewTypeOptionsConfigurablePropsV1 } from '@/contracts/catalog/product/view/type/options_configurable'

const props = defineProps<CatalogProductViewTypeOptionsConfigurablePropsV1>()
const attributes = computed(() => props.attributes || [])
const jsonConfig = computed(() => props.jsonConfig || null)
const isSaleable = computed(() => props.isSaleable || false)

onMounted(() => {
  if (jsonConfig.value && (window as any).Product && (window as any).Product.Config) {
    ; (window as any).spConfig = new (window as any).Product.Config(jsonConfig.value)
  }
})
</script>
