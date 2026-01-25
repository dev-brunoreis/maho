<template>
    <div>
        <div id="messages_product_view" v-html="messagesHtml"></div>
        <div class="product-view">
            <div class="product-essential">
                <form :action="submitUrl" method="post" id="product_addtocart_form"
                    :enctype="hasOptions ? 'multipart/form-data' : ''">
                    <div v-html="formKeyHtml"></div>
                    <div class="no-display">
                        <input type="hidden" name="product" :value="productId" />
                        <input type="hidden" name="related_product" id="related-products-field" value="" />
                    </div>

                    <div class="product-img-box">
                        <div class="product-name">
                            <h1 v-html="productName"></h1>
                        </div>
                        <slot name="media"></slot>
                    </div>

                    <div class="product-shop">
                        <div class="product-name">
                            <span class="h1" v-html="productName"></span>
                        </div>

                        <div class="price-info">
                            <slot name="price"></slot>
                            <slot name="bundle_prices"></slot>
                            <slot name="tier_price"></slot>
                        </div>

                        <div class="extra-info">
                            <div v-html="reviewsSummaryHtml"></div>
                            <slot name="product_type_availability"></slot>
                        </div>

                        <slot name="alert_urls"></slot>

                        <div v-if="shortDescription" class="short-description">
                            <div class="std" v-html="shortDescription"></div>
                        </div>

                        <slot name="other"></slot>

                        <div v-if="isSaleable && hasOptions">
                            <slot name="container1"></slot>
                        </div>
                    </div>

                    <div class="add-to-cart-wrapper">
                        <slot name="product_type_data"></slot>
                        <slot name="extrahint"></slot>

                        <div v-if="!hasOptions" class="add-to-box">
                            <slot v-if="isSaleable" name="addtocart"></slot>
                            <slot name="addto"></slot>
                            <slot name="sharing"></slot>
                        </div>
                        <slot v-if="!hasOptions" name="extra_buttons"></slot>
                        <div v-else-if="!isSaleable" class="add-to-box">
                            <slot name="addto"></slot>
                            <slot name="sharing"></slot>
                        </div>
                    </div>

                    <slot name="related_products"></slot>

                    <div class="clearer"></div>
                    <div v-if="isSaleable && hasOptions">
                        <slot name="container2"></slot>
                    </div>
                </form>
            </div>

            <div class="product-collateral">
                <div v-if="hasDetailedInfoGroup" class="collateral-tabs-wrapper">
                    <input v-for="(html, alias, index) in detailedInfoGroup" :key="alias" type="radio"
                        :name="'collateral-tab'" :id="'tab-' + alias" class="tab-selector" :checked="index === 0" />

                    <ul class="collateral-tab-list">
                        <li v-for="(html, alias) in detailedInfoGroup" :key="alias">
                            <label :for="'tab-' + alias" v-html="getTabTitle(alias)"></label>
                        </li>
                    </ul>

                    <div class="collateral-tabs-content">
                        <div v-for="(html, alias) in detailedInfoGroup" :key="alias" class="tab-panel"
                            :id="'panel-' + alias" :data-title="getTabTitle(alias)" v-html="html"></div>
                    </div>
                </div>
            </div>

            <slot name="upsell_products"></slot>
            <slot name="product_additional_data"></slot>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useOpenwire } from '@/bridge/mount.js'
import type { CatalogProductViewPropsV1 } from '@/contracts/catalog/product/view'

const { call } = useOpenwire()
const props = defineProps<CatalogProductViewPropsV1>()
const productId = computed(() => props.productId)
const isSaleable = computed(() => props.isSaleable)

// Props from phtml
const messagesHtml = computed(() => props.messagesHtml || '')
const formKeyHtml = computed(() => props.formKeyHtml || '')
const reviewsSummaryHtml = computed(() => props.reviewsSummaryHtml || '')
const shortDescription = computed(() => props.shortDescription || '')
const productName = computed(() => props.productName || '')
const submitUrl = computed(() => props.submitUrl || '')
const hasOptions = computed(() => props.hasOptions || false)
const detailedInfoGroup = computed(() => props.detailedInfoGroup || null)
const optionsPriceConfig = computed(() => props.optionsPriceConfig || null)
const hasDetailedInfoGroup = computed(() => {
    const group = detailedInfoGroup.value
    return Boolean(group && Object.keys(group).length > 0)
})

// Methods
const getTabTitle = (alias: string) => {
    // This will be provided by the parent component or via props
    return alias
}

onMounted(() => {
    // Initialize optionsPrice if config is available
    if (optionsPriceConfig.value && window.Product && window.Product.OptionsPrice) {
        window.optionsPrice = new window.Product.OptionsPrice(optionsPriceConfig.value)
    }

    // Initialize product form - wait a bit for DOM to be ready
    setTimeout(() => {
        if (window.VarienForm) {
            const formElement = document.getElementById('product_addtocart_form')
            if (formElement) {
                window.productAddToCartForm = new window.VarienForm('product_addtocart_form')

                // Override submit method
                if (window.productAddToCartForm) {
                    window.productAddToCartForm.submit = async function (button, url) {
                        if (!this.validator.validate()) {
                            return
                        }

                        const form = this.form
                        const targetUrl = url || form.action

                        if (button && button !== 'undefined') {
                            button.disabled = true
                        }

                        try {
                            const formData = new FormData(form)
                            // FormData iteration types vary by TS/lib versions; normalize via entries().
                            const payload = Object.fromEntries((formData as any).entries())
                            const result = await call('submit', payload, {})

                            if (result.success && typeof window.minicart !== 'undefined') {
                                window.minicart.updateCartQty(result.qty)
                                window.minicart.updateContent(result)
                                window.minicart.init()
                                window.minicart.openOffcanvas()
                                setTimeout(() => window.minicart.showMessage(result), 100)
                            } else if (result.error) {
                                alert(result.error)
                            }
                        } catch (error) {
                            alert((error as any).message || 'Cannot add the item to shopping cart.')
                        } finally {
                            if (button && button !== 'undefined') {
                                button.disabled = false
                            }
                        }
                    }.bind(window.productAddToCartForm)

                    window.productAddToCartForm.submitLight = function (button, url) {
                        if (this.validator && window.Validation) {
                            const originalMethods = { ...window.Validation.methods }
                            delete window.Validation.methods['required-entry']
                            delete window.Validation.methods['validate-one-required']
                            delete window.Validation.methods['validate-one-required-by-name']

                            Object.keys(window.Validation.methods).forEach(methodName => {
                                if (methodName.match(/^validate-datetime-.*/i)) {
                                    delete window.Validation.methods[methodName]
                                }
                            })

                            if (this.validator.validate()) {
                                if (url) {
                                    this.form.action = url
                                }
                                this.form.submit()
                            }

                            window.Validation.methods = { ...originalMethods }
                        }
                    }.bind(window.productAddToCartForm)
                }
            }
        }
    }, 100)
})
</script>
