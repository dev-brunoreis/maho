// Event Handler

import { ajaxClient } from './ajax-client';
import { responseHandler } from './response-handler';
import { debouncer } from './debouncer';

export class EventHandler {
  private boundClickHandler: (e: Event) => void;
  private boundChangeHandler: (e: Event) => void;
  private boundInputHandler: (e: Event) => void;
  private boundSubmitHandler: (e: Event) => void;

  constructor() {
    this.boundClickHandler = this.handleClick.bind(this);
    this.boundChangeHandler = this.handleChange.bind(this);
    this.boundInputHandler = this.handleInput.bind(this);
    this.boundSubmitHandler = this.handleSubmit.bind(this);
  }

  attach(): void {
    document.addEventListener('click', this.boundClickHandler);
    document.addEventListener('change', this.boundChangeHandler);
    document.addEventListener('input', this.boundInputHandler);
    document.addEventListener('submit', this.boundSubmitHandler);
  }

  detach(): void {
    document.removeEventListener('click', this.boundClickHandler);
    document.removeEventListener('change', this.boundChangeHandler);
    document.removeEventListener('input', this.boundInputHandler);
    document.removeEventListener('submit', this.boundSubmitHandler);
  }

  private async handleClick(e: Event): Promise<void> {
    const target = e.target as HTMLElement;
    const method = target.getAttribute('data-ow:click') || target.getAttribute('data-openwire:click');
    if (method) {
      e.preventDefault();
      const element = target.closest('[data-ow-id],[data-openwire]') as HTMLElement;
      const id = element?.getAttribute('data-ow-id') || element?.getAttribute('data-openwire-id');
      const component = element?.getAttribute('data-ow-component') || element?.getAttribute('data-openwire-component') || element?.getAttribute('data-openwire');
      if (id && component) {
        const payload: any = { id, component, calls: [{ method, params: [] }] };
        
        // Send initial state if available and not sent before
        if (!element.__openwire_state_sent) {
          const configAttr = element.getAttribute('data-ow-config') || element.getAttribute('data-openwire-config');
          if (configAttr) {
            try {
              const config = JSON.parse(configAttr);
              if (config.initialState) {
                payload.initial_state = config.initialState;
                element.__openwire_state_sent = true;
              }
            } catch (e) {
              // Ignore parse errors
            }
          }
        }
        
        const response = await ajaxClient.sendUpdate(payload);
        responseHandler.handle(response, element);
      }
    }
  }

  private handleChange(e: Event): void {
    const target = e.target as HTMLInputElement;
    const method = target.getAttribute('data-ow:change') || target.getAttribute('data-openwire:change');
    if (method) {
      const element = target.closest('[data-ow-id],[data-openwire]') as HTMLElement;
      const id = element?.getAttribute('data-ow-id') || element?.getAttribute('data-openwire-id');
      const component = element?.getAttribute('data-openwire-component') || element?.getAttribute('data-openwire') || element?.getAttribute('data-ow-component');
      if (id && component) {
        const payload: any = { id, component, calls: [{ method, params: [target.value] }] };
        
        // Send initial state if available and not sent before
        if (!element.__openwire_state_sent) {
          const configAttr = element.getAttribute('data-ow-config') || element.getAttribute('data-openwire-config');
          if (configAttr) {
            try {
              const config = JSON.parse(configAttr);
              if (config.initialState) {
                payload.initial_state = config.initialState;
                element.__openwire_state_sent = true;
              }
            } catch (e) {
              // Ignore parse errors
            }
          }
        }
        
        debouncer.debounce(async () => {
          const response = await ajaxClient.sendUpdate(payload);
          responseHandler.handle(response, element);
        }, 300, id + method);
      }
    }
  }

  private handleInput(e: Event): void {
    const target = e.target as HTMLInputElement;
    const method = target.getAttribute('data-ow:input') || target.getAttribute('data-openwire:input');
    if (method) {
      const element = target.closest('[data-ow-id],[data-openwire]') as HTMLElement;
      const id = element?.getAttribute('data-ow-id') || element?.getAttribute('data-openwire-id');
      const component = element?.getAttribute('data-openwire-component') || element?.getAttribute('data-openwire') || element?.getAttribute('data-ow-component');
      if (id && component) {
        const payload: any = { id, component, calls: [{ method, params: [target.value] }] };
        
        // Send initial state if available and not sent before
        if (!element.__openwire_state_sent) {
          const configAttr = element.getAttribute('data-ow-config') || element.getAttribute('data-openwire-config');
          if (configAttr) {
            try {
              const config = JSON.parse(configAttr);
              if (config.initialState) {
                payload.initial_state = config.initialState;
                element.__openwire_state_sent = true;
              }
            } catch (e) {
              // Ignore parse errors
            }
          }
        }
        
        debouncer.debounce(async () => {
          const response = await ajaxClient.sendUpdate(payload);
          responseHandler.handle(response, element);
        }, 300, id + method);
      }
    }
  }

  private async handleSubmit(e: Event): Promise<void> {
    const form = e.target as HTMLFormElement;
    const method = form.getAttribute('data-ow:submit') || form.getAttribute('data-openwire:submit');
    if (method) {
      e.preventDefault();
      const element = form.closest('[data-ow-id],[data-openwire]') as HTMLElement;
      const id = element?.getAttribute('data-ow-id') || element?.getAttribute('data-openwire-id');
      const component = element?.getAttribute('data-ow-component') || element?.getAttribute('data-openwire-component') || element?.getAttribute('data-openwire');
      if (id && component) {
        const formData = new FormData(form);
        const params = Object.fromEntries(formData);
        const payload: any = { id, component, calls: [{ method, params: [params] }] };
        
        // Send initial state if available and not sent before
        if (!element.__openwire_state_sent) {
          const configAttr = element.getAttribute('data-ow-config') || element.getAttribute('data-openwire-config');
          if (configAttr) {
            try {
              const config = JSON.parse(configAttr);
              if (config.initialState) {
                payload.initial_state = config.initialState;
                element.__openwire_state_sent = true;
              }
            } catch (e) {
              // Ignore parse errors
            }
          }
        }
        
        const response = await ajaxClient.sendUpdate(payload);
        responseHandler.handle(response, element);
      }
    }
  }
}

export const eventHandler = new EventHandler();