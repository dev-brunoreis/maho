import { inject } from 'vue'
import client from './openwire-client.js'

export function useOpenwire() {
  const props = inject('props', {})
  const openwire = inject('openwire', '')
  const call = (action, payload, state) => {
    return client.call(openwire, action, payload, state)
  }
  return { call, props }
}