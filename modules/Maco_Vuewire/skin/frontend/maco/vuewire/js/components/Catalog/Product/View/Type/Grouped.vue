<template>
  <div>
    <slot name="product_type_data_extra"></slot>
    <div class="grouped-items-table-wrapper">
      <table class="data-table grouped-items-table" id="super-product-table">
        <col />
        <col />
        <col />
        <tbody>
          <template v-if="hasAssociatedProducts">
            <tr v-for="item in associatedProducts" :key="item.id">
              <td class="image">
                <img class="thumbnail" :src="item.thumbnail" :srcset="item.thumbnail2x + ' 2x'" :alt="item.image_label"
                  :title="item.image_label" />
              </td>
              <td class="name">
                <p class="name-wrapper">{{ item.name }}</p>
                <div v-if="isSaleable" class="qty-wrapper">
                  <template v-if="item.is_saleable">
                    <input :id="'super_group_' + item.id" type="text" pattern="\d*(\.\d+)?"
                      :name="'super_group[' + item.id + ']'" maxlength="12" :value="item.qty" :title="$t('Qty')"
                      class="input-text qty" />
                    <label :for="'super_group_' + item.id" class="qty-label">
                      {{ $t('Quantity') }}
                    </label>
                  </template>
                  <p v-else class="availability out-of-stock">
                    <span>{{ $t('Out of stock') }}</span>
                  </p>
                </div>
              </td>
              <td v-if="canShowProductPrice" class="a-right">
                <div v-if="canShowItemPrice(item)" v-html="item.price_html"></div>
                <div v-if="canShowItemPrice(item)" v-html="item.tier_price_html"></div>
              </td>
            </tr>
          </template>
          <tr v-else>
            <td :colspan="isSaleable ? 4 : 3">
              {{ $t('No options of this product are available.') }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewTypeGroupedPropsV1 } from '@/contracts/catalog/product/view/type/grouped'

const props = defineProps<CatalogProductViewTypeGroupedPropsV1>()
const isSaleable = computed(() => props.isSaleable || false)
const associatedProducts = computed(() => props.associatedProducts || [])
const hasAssociatedProducts = computed(() => props.hasAssociatedProducts || false)
const canShowProductPrice = computed(() => props.canShowProductPrice || false)

const canShowItemPrice = (_item: CatalogProductViewTypeGroupedPropsV1['associatedProducts'][number]) => {
  // This would need to match the PHP logic
  return true
}
</script>
