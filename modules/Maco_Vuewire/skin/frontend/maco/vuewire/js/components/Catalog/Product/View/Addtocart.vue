<template>
  <div v-if="isSaleable" class="add-to-cart">
    <div v-if="!isGrouped" class="qty-wrapper">
      <label for="qty">{{ $t('Qty:') }}</label>
      <input type="text" pattern="\d*(\.\d+)?" name="qty" id="qty" maxlength="12" :value="defaultQty" :title="$t('Qty')"
        class="input-text qty" aria-required="true" />
    </div>
    <div class="add-to-cart-buttons">
      <button type="button" :title="buttonTitle" class="button btn-cart" @click="handleAddToCart">
        {{ buttonTitle }}
      </button>
      <slot name="children"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewAddtocartPropsV1 } from '@/contracts/catalog/product/view/addtocart'

const props = defineProps<CatalogProductViewAddtocartPropsV1>()
const buttonTitle = computed(() => props.buttonTitle || 'Add to Cart')
const defaultQty = computed(() => props.defaultQty || 1)
const isGrouped = computed(() => props.isGrouped || false)
const isSaleable = computed(() => props.isSaleable || false)

const handleAddToCart = () => {
  if (window.productAddToCartForm) {
    const button = document.querySelector('.btn-cart')
    window.productAddToCartForm.submit(button)
  }
}
</script>
