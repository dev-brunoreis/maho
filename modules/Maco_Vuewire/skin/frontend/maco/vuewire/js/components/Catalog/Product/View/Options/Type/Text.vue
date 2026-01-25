<template>
  <dt>
    <label :class="{ required: option.isRequired }" :for="'option_' + option.id">
      {{ option.title }}
    </label>
    <span v-html="formatedPrice"></span>
  </dt>
  <dd>
    <div class="input-box">
      <input v-if="option.type === 'field'" type="text" :id="'option_' + option.id"
        :class="'input-text' + (option.isRequired ? ' required-entry' : '') + (option.maxCharacters ? ' validate-length maximum-length-' + option.maxCharacters : '') + ' product-custom-option'"
        :name="'options[' + option.id + ']'" :value="option.defaultValue" @change="reloadPrice" />
      <textarea v-else-if="option.type === 'area'" :id="'option_' + option.id"
        :class="(option.isRequired ? ' required-entry' : '') + (option.maxCharacters ? ' validate-length maximum-length-' + option.maxCharacters : '') + ' product-custom-option'"
        :name="'options[' + option.id + ']'" rows="5" cols="25"
        @change="reloadPrice">{{ option.defaultValue }}</textarea>
      <p v-if="option.maxCharacters" class="note">
        {{ $t('Maximum number of characters:') }} <strong>{{ option.maxCharacters }}</strong>
      </p>
    </div>
  </dd>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewOptionsTypeTextPropsV1 } from '@/contracts/catalog/product/view/options/type/text'

const props = defineProps<CatalogProductViewOptionsTypeTextPropsV1>()
const option = computed(() => props.option)
const formatedPrice = computed(() => props.formatedPrice || '')

const reloadPrice = () => {
  if (window.opConfig && window.opConfig.reloadPrice) {
    window.opConfig.reloadPrice()
  }
}
</script>
