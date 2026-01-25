// Bootstrapper

import { poller } from './poller';
import { eventHandler } from './event-handler';

export class Bootstrapper {
  bootstrap(): void {
    // Attach event handlers
    eventHandler.attach();

    // Auto-bootstrap OpenWire components
    document.querySelectorAll('[data-ow-component],[data-openwire]').forEach((el) => {
      const element = el as HTMLElement;
      if (!element.__openwire) {
        element.__openwire = true;
        // Set component attribute for backend resolution
        const componentName = element.getAttribute('data-ow-component') || element.getAttribute('data-openwire');
        if (componentName && !element.hasAttribute('data-ow-component')) {
          element.setAttribute('data-ow-component', componentName);
        }
        // Generate unique ID if not present
        if (!element.hasAttribute('data-ow-id')) {
          element.setAttribute('data-ow-id', `ow_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`);
        }
        // Initialize Alpine if not already done
        if (!element.hasAttribute('x-data')) {
          element.setAttribute('x-data', '{}');
        }
      }
    });

    document.querySelectorAll('[data-ow-component]').forEach((el) => {
      const element = el as HTMLElement;
      const configAttr = element.getAttribute('data-ow-config');
      if (configAttr) {
        try {
          const config = JSON.parse(configAttr);
          if (config.pollIntervalMs) {
            poller.start(element, config.pollIntervalMs);
          }
        } catch (e) {
          console.error('Invalid data-ow-config:', configAttr);
        }
      }
    });
  }
}

export const bootstrapper = new Bootstrapper();