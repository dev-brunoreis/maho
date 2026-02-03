import './main.css'
import { createApp, h } from 'vue'
import registry from './bridge/registry.js'

// NOTE: `app.js` is included by Magento as a classic `<script>` (not `type="module"`),
// so we must not use `import.meta` here. Opt-in dev diagnostics via a global flag:
// `window.__VUEWIRE_DEV__ = true`.
const DEV = Boolean(globalThis && globalThis.__VUEWIRE_DEV__)

function interpolate(template, params) {
  if (!params || typeof params !== 'object') return template
  return Object.entries(params).reduce((acc, [key, value]) => {
    return acc.replaceAll(`{${key}}`, String(value))
  }, template)
}

function createTranslator() {
  return (key, params) => {
    let text = String(key ?? '')

    // Prefer Magento/Maho-style translator if present.
    try {
      const translator = window?.Translator
      if (translator && typeof translator.translate === 'function') {
        text = translator.translate(text)
      }
    } catch {
      // ignore
    }

    return interpolate(text, params)
  }
}

function createLogPrefix({ contract, componentName }) {
  const parts = []
  if (contract) parts.push(contract)
  if (componentName) parts.push(componentName)
  return parts.length ? `[vuewire:${parts.join(':')}]` : '[vuewire]'
}

function safeParseJson(json, fallback, logPrefix) {
  try {
    return JSON.parse(json)
  } catch (err) {
    if (DEV) {
      console.warn(`${logPrefix} Invalid JSON in data-props.`, { json, err })
    }
    return fallback
  }
}

function extractSlotHtml(rootEl) {
  const slotEls = rootEl.querySelectorAll(':scope > slot[name]')
  const slots = {}
  slotEls.forEach((slotEl) => {
    const name = slotEl.getAttribute('name')
    if (!name) return
    slots[name] = slotEl.innerHTML
  })
  return slots
}

function makeSlotMarker(contract, name) {
  // Comments are inert, but allow us to detect when slot HTML was actually rendered.
  // Keep it short to avoid bloating HTML.
  return `vw:${contract || 'no_contract'}:${name}`
}

function decorateSlotsForDev(slotsHtml, contract) {
  if (!DEV) return slotsHtml
  const decorated = {}
  Object.entries(slotsHtml).forEach(([name, html]) => {
    const marker = makeSlotMarker(contract, name)
    decorated[name] = `<!--${marker}:start-->${html}<!--${marker}:end-->`
  })
  return decorated
}

function toVueSlots(slotsHtml) {
  const vueSlots = {}
  Object.entries(slotsHtml).forEach(([name, html]) => {
    // Use `display: contents` to avoid layout wrappers for most cases.
    vueSlots[name] = () => h('span', { style: 'display: contents', innerHTML: html })
  })
  return vueSlots
}

function warnIfSlotsNotRendered({ el, slotsHtml, contract, logPrefix }) {
  if (!DEV) return
  const html = el?.innerHTML || ''
  Object.entries(slotsHtml).forEach(([name, slotHtml]) => {
    if (!slotHtml || !String(slotHtml).trim()) return
    const marker = makeSlotMarker(contract, name)
    if (!html.includes(marker)) {
      console.warn(
        `${logPrefix} Slot content '${name}' existed in PHTML but was not rendered by the Vue component. ` +
          `Add <slot name="${name}" /> (preferred) or render props.slots["${name}"] via v-html.`
      )
    }
  })
}

function getDepth(el) {
  let d = 0
  let node = el
  while (node && node.parentElement) {
    d += 1
    node = node.parentElement
  }
  return d
}

document.addEventListener('DOMContentLoaded', () => {
  const elements = Array.from(document.querySelectorAll('[data-ui^="vue:"]'))
  // Mount nested components first (deepest in DOM first) so parent slots contain rendered children
  elements.sort((a, b) => getDepth(b) - getDepth(a))

  elements.forEach((el) => {
    if (!el.isConnected) return
    const ui = el.getAttribute('data-ui')
    const componentName = ui.split(':')[1]
    const componentPromise = registry[componentName]
    if (componentPromise) {
      componentPromise().then(component => {
        const contract = el.getAttribute('data-contract') || ''
        const logPrefix = createLogPrefix({ contract, componentName })
        if (DEV && !contract) {
          console.warn(
            `${logPrefix} Missing data-contract on mount element. ` +
              `Add a versioned contract id (example: data-contract="catalog/product_view@1") to improve DX.`
          )
        }

        const rawProps = safeParseJson(el.getAttribute('data-props') || '{}', {}, logPrefix)
        const extractedSlots = extractSlotHtml(el)
        const decoratedSlots = decorateSlotsForDev(extractedSlots, contract)
        const props = {
          ...rawProps,
          slots: {
            ...(rawProps.slots || {}),
            ...decoratedSlots,
          },
        }

        // Backwards-compatible prop aliases
        if (props.productId == null && props.product != null) {
          props.productId = props.product
        }
        if (!props.priceHtml && props.slots && props.slots.price) {
          props.priceHtml = props.slots.price
        }
        if (!props.tierPriceHtml && props.slots && props.slots.tier_price) {
          props.tierPriceHtml = props.slots.tier_price
        }

        const openwire =
          el.getAttribute('data-openwire') || el.getAttribute('openwire') || ''

        const slotFns = toVueSlots(props.slots || {})

        const app = createApp({
          render() {
            return h(component.default, props, slotFns)
          },
        })

        // Provide `$t(...)` to templates (used throughout components).
        app.config.globalProperties.$t = createTranslator()

        // Backwards compatibility (legacy components still inject these)
        app.provide('props', props)
        app.provide('openwire', openwire)
        app.provide('contract', contract)
        app.mount(el)

        // DX warning: detect when PHTML slot content was not surfaced by Vue.
        setTimeout(() => {
          warnIfSlotsNotRendered({ el, slotsHtml: decoratedSlots, contract, logPrefix })
        }, 0)
      })
    }
  })
})
