<template>
  <dt>
    <label :class="{ required: option.isRequired }" :for="'option_' + option.id">
      {{ option.title }}
    </label>
    <span v-html="formatedPrice"></span>
  </dt>
  <dd>
    <div v-if="option.type === 'date_time' || option.type === 'date'" v-html="dateHtml"></div>
    <span v-if="option.type === 'time'" class="time-picker" v-html="timeHtml"></span>
    <input :id="'option_' + option.id" type="hidden" :name="'validate_datetime_' + option.id"
      :class="'validate-datetime-' + option.id" value="" />
  </dd>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import type { CatalogProductViewOptionsTypeDatePropsV1 } from '@/contracts/catalog/product/view/options/type/date'

const props = defineProps<CatalogProductViewOptionsTypeDatePropsV1>()
const option = computed(() => props.option)
const formatedPrice = computed(() => props.formatedPrice || '')
const dateHtml = computed(() => props.dateHtml || '')
const timeHtml = computed(() => props.timeHtml || '')

onMounted(() => {
  const optionId = option.value.id
  if (option.value.isRequired) {
    if (window.Validation) {
      window.Validation.add(
        'validate-datetime-' + optionId,
        'This is a required option',
        function (v, el) {
          const parts = el.parentNode.querySelectorAll('.datetime-picker[id^="options_' + optionId + '"]:not([id$=day_part])')
          return [...parts].every(el => el.value !== '')
        }
      )
    }
  } else {
    if (window.Validation) {
      window.Validation.add(
        'validate-datetime-' + optionId,
        'Field is not complete',
        function (v, el) {
          const parts = el.parentNode.querySelectorAll('.datetime-picker[id^="options_' + optionId + '"]:not([id$=day_part])')
          return [...parts].every(el => el.value === '')
            || [...parts].every(el => el.value !== '')
        }
      )
    }
  }
})
</script>
