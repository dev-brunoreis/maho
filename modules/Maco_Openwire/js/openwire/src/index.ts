// OpenWire JavaScript Runtime

import Alpine from 'alpinejs';

// Import instances
import { pluginManager } from './plugin-manager';
import { effectManager } from './effect-manager';
import { ajaxClient } from './ajax-client';
import { responseHandler } from './response-handler';
import { debouncer } from './debouncer';
import { poller } from './poller';
import { bootstrapper } from './bootstrapper';
import { eventHandler } from './event-handler';
import { initDebugPanel } from './debug-panel';

// Import classes/utilities
import { FormUtils } from './form-utils';
import { DomPatcher } from './dom-patcher';

// Re-export types
export type { Call, UpdatePayload, Effect, Response, ComponentConfig } from './types';

// Re-export utilities
export { pluginManager, PluginManager } from './plugin-manager';
export { effectManager, EffectManager } from './effect-manager';
export { FormUtils } from './form-utils';
export { ajaxClient, AjaxClient } from './ajax-client';
export { responseHandler, ResponseHandler } from './response-handler';
export { DomPatcher } from './dom-patcher';
export { debouncer, Debouncer } from './debouncer';
export { poller, Poller } from './poller';
export { bootstrapper, Bootstrapper } from './bootstrapper';
export { eventHandler, EventHandler } from './event-handler';

// Legacy exports for backward compatibility
export function registerOpenWirePlugin(plugin: any) {
  pluginManager.register(plugin);
}

export function registerEffectHandler(type: string, handler: Function) {
  effectManager.register(type, handler);
}

export function getFormKey(): string | undefined {
  return FormUtils.getFormKey();
}

export async function sendUpdate(payload: any): Promise<any> {
  return ajaxClient.sendUpdate(payload);
}

export function handleResponse(response: any, element: HTMLElement) {
  responseHandler.handle(response, element);
}

export function patch(element: HTMLElement, html: string) {
  const domPatcher = new DomPatcher();
  domPatcher.patch(element, html);
}

export function debounce(func: Function, wait: number, key: string) {
  debouncer.debounce(func, wait, key);
}

export function startPolling(element: HTMLElement, interval: number) {
  poller.start(element, interval);
}

export function bootstrap() {
  bootstrapper.bootstrap();
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  bootstrapper.bootstrap();
  eventHandler.attach();
  initDebugPanel();
  // Start Alpine after DOM is ready
  Alpine.start();
});

(window as any).OpenWire = {
  registerPlugin: registerOpenWirePlugin,
  registerEffectHandler,
  sendUpdate,
  bootstrap,
};

// Make Alpine available globally
(window as any).Alpine = Alpine;
