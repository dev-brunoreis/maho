<template>
  <dt>
    <label :class="{ required: option.isRequired }" :for="'option_' + option.id">
      {{ option.title }}
    </label>
    <span v-html="formatedPrice"></span>
  </dt>
  <dd>
    <span v-if="fileInfo" :class="fileNamed">{{ fileInfo.title }}</span>
    <a v-if="fileInfo" href="javascript:void(0)" class="label" @click="toggleFileChange">
      {{ $t('Change') }}
    </a>
    <label v-if="fileInfo && !option.isRequired">
      <input type="checkbox" @click="toggleFileDelete" />
      {{ $t('Delete') }}
    </label>
    <div :class="'input-box' + (fileInfo ? ' no-display' : '')">
      <input type="file" :id="'option_' + option.id" :name="fileName"
        :class="'product-custom-option' + (option.isRequired ? ' required-entry' : '')"
        :disabled="fileInfo ? 'disabled' : null" @change="reloadPrice" />
      <input type="hidden" :name="fieldNameAction" :value="fieldValueAction" />
      <p v-if="sanitizedExtensions" class="no-margin">
        {{ $t('Allowed file extensions to upload') }}: <strong>{{ sanitizedExtensions }}</strong>
      </p>
      <p v-if="option.imageSizeX > 0" class="no-margin">
        {{ $t('Maximum image width') }}: <strong>{{ option.imageSizeX }} {{ $t('px.') }}</strong>
      </p>
      <p v-if="option.imageSizeY > 0" class="no-margin">
        {{ $t('Maximum image height') }}: <strong>{{ option.imageSizeY }} {{ $t('px.') }}</strong>
      </p>
      <p class="no-margin">
        {{ $t('Maximum file size') }}: <strong>{{ maxFileSizeMb }} MB</strong>
      </p>
    </div>
  </dd>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CatalogProductViewOptionsTypeFilePropsV1 } from '@/contracts/catalog/product/view/options/type/file'

const props = defineProps<CatalogProductViewOptionsTypeFilePropsV1>()
const option = computed(() => props.option)
const formatedPrice = computed(() => props.formatedPrice || '')
const fileInfo = computed(() => props.fileInfo || null)
const fileName = computed(() => props.fileName || '')
const fieldNameAction = computed(() => props.fieldNameAction || '')
const fieldValueAction = computed(() => props.fieldValueAction || '')
const fileNamed = computed(() => props.fileNamed || '')
const sanitizedExtensions = computed(() => props.sanitizedExtensions || '')
const maxFileSizeMb = computed(() => String(props.maxFileSizeMb ?? ''))

let fileChangeFlag = false
let fileDeleteFlag = false

const toggleFileChange = () => {
  const inputBox = document.querySelector('.input-box') as HTMLElement | null
  if (!inputBox) return
  inputBox.classList.remove('no-display')
  fileChangeFlag = !fileChangeFlag
  if (!fileDeleteFlag) {
    const inputFile = inputBox.querySelector(`input[name="${fileName.value}"]`) as HTMLInputElement | null
    const inputFileAction = inputBox.querySelector(`input[name="${fieldNameAction.value}"]`) as HTMLInputElement | null
    if (!inputFile || !inputFileAction) return
    if (fileChangeFlag) {
      inputFileAction.value = 'save_new'
      inputFile.disabled = false
    } else {
      inputFileAction.value = 'save_old'
      inputFile.disabled = true
    }
  }
}

const toggleFileDelete = (e: Event) => {
  const target = e.target as HTMLInputElement | null
  fileDeleteFlag = Boolean(target?.checked)
  const inputBox = document.querySelector('.input-box') as HTMLElement | null
  if (!inputBox) return
  const inputFile = inputBox.querySelector(`input[name="${fileName.value}"]`) as HTMLInputElement | null
  const inputFileAction = inputBox.querySelector(`input[name="${fieldNameAction.value}"]`) as HTMLInputElement | null
  const fileNameBox = document.querySelector(`.${fileNamed.value}`) as HTMLElement | null
  if (!inputFileAction || !inputFile) return

  if (fileDeleteFlag) {
    inputFileAction.value = ''
    inputFile.disabled = true
    if (fileNameBox) fileNameBox.style.textDecoration = 'line-through'
  } else {
    inputFileAction.value = fileChangeFlag ? 'save_new' : 'save_old'
    inputFile.disabled = fileChangeFlag === 'save_old'
    if (fileNameBox) fileNameBox.style.textDecoration = null
  }
}

const reloadPrice = () => {
  if (window.opConfig && window.opConfig.reloadPrice) {
    window.opConfig.reloadPrice()
  }
}
</script>
