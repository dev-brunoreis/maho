<template>
  <div>
    <div class="product-image" role="region" :aria-label="$t('Product Images')">
      <div class="product-image-gallery">
        <img id="image-main" class="gallery-image visible" :src="mainImage" :alt="mainImageLabel"
          :title="mainImageLabel" :width="mainImageWidth" :height="mainImageHeight" />

        <img v-for="(image, index) in galleryImages" :key="index" :id="'image-' + index" class="gallery-image"
          loading="lazy" :src="image.url" :data-zoom-image="image.url" :alt="image.label + ' ' + (index + 1)" />
      </div>
    </div>

    <div v-if="galleryImages.length > 0" class="more-views" role="region" :aria-label="$t('Additional Views')">
      <ul class="product-image-thumbs">
        <li v-for="(image, index) in galleryImages" :key="index">
          <a class="thumb-link" href="#" :title="image.label + ' ' + (index + 1)" :data-image-index="index"
            role="button" :aria-label="$t('View image {index}', { index: index + 1 })">
            <img :src="image.thumbnail" :srcset="image.thumbnail2x + ' 2x'" width="75" height="75"
              :alt="image.label + ' ' + (index + 1)" />
          </a>
        </li>
      </ul>
    </div>

    <slot name="after"></slot>
  </div>
</template>

<script setup lang="ts">
import type { CatalogProductViewMediaPropsV1 } from '@/contracts/catalog/product/view/media'

defineProps<CatalogProductViewMediaPropsV1>()
</script>
