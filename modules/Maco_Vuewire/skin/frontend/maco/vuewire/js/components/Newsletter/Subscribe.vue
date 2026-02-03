<template>
  <div class="block block-subscribe">
    <div class="block-title">{{ $t('Newsletter') }}</div>
    <form
      id="newsletter-validate-detail"
      :action="formActionUrl"
      method="post"
    >
      <slot name="formKey"></slot>
      <div class="block-content">
        <div class="input-box">
          <label for="newsletter" class="visually-hidden">
            {{ $t('Sign Up for Our Newsletter:') }}
          </label>
          <input
            id="newsletter"
            v-model="email"
            type="email"
            name="email"
            autocomplete="on"
            autocapitalize="off"
            autocorrect="off"
            spellcheck="false"
            class="input-text required-entry validate-email"
            :title="$t('Sign up for our newsletter')"
            aria-required="true"
          />
        </div>
        <div class="actions">
          <button type="submit" class="button" :title="$t('Subscribe')">
            {{ $t('Subscribe') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { NewsletterSubscribePropsV1 } from '@/contracts/newsletter/subscribe'

const props = defineProps<NewsletterSubscribePropsV1>()
const email = ref('')

onMounted(() => {
  if (
    typeof window !== 'undefined' &&
    (window as unknown as { Varien?: { Form?: unknown } }).Varien?.Form
  ) {
    const VarienForm = (window as unknown as {
      Varien: { Form: new (id: string) => void }
    }).Varien.Form
    new VarienForm('newsletter-validate-detail')
  }
})
</script>
