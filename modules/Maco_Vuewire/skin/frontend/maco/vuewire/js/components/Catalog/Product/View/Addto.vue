<template>
  <div v-if="wishlistSubmitUrl || compareUrl" class="add-to-links">
    <a v-if="wishlistSubmitUrl" :href="wishlistSubmitUrl" class="link-wishlist" :title="$t('Add to Wishlist')"
      @click.prevent="handleWishlist">
      <svg-icon name="heart"></svg-icon>
    </a>
    <a v-if="compareUrl" href="#" class="link-compare" :title="$t('Add to Compare')" @click.prevent="handleCompare">
      <svg-icon name="scale"></svg-icon>
    </a>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewAddtoPropsV1 } from '@/contracts/catalog/product/view/addto'
import { useOpenwire } from '@/bridge/mount.js'

const props = defineProps<CatalogProductViewAddtoPropsV1>()
const wishlistSubmitUrl = computed(() => props.wishlistSubmitUrl || '')
const compareUrl = computed(() => props.compareUrl || '')
const formKey = computed(() => props.formKey || '')
useOpenwire()

const handleWishlist = (e) => {
  if (window.productAddToCartForm) {
    window.productAddToCartForm.submitLight(e.target, wishlistSubmitUrl.value)
  }
}

const handleCompare = () => {
  if (window.customFormSubmit && compareUrl.value) {
    const params = JSON.stringify({ form_key: formKey.value })
    window.customFormSubmit(compareUrl.value, params, 'post')
  }
}
</script>
