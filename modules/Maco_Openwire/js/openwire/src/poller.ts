// Polling Manager

import { ajaxClient } from './ajax-client';
import { responseHandler } from './response-handler';

export class Poller {
  private timers: { [key: string]: number } = {};

  start(element: HTMLElement, interval: number): void {
    const id = element.getAttribute('data-ow-id');
    const component = element.getAttribute('data-ow-component');
    if (!id || !component) return;
    this.stop(id);
    this.timers[id] = setInterval(async () => {
      const response = await ajaxClient.sendUpdate({ id, component });
      responseHandler.handle(response, element);
    }, interval);
  }

  stop(id: string): void {
    if (this.timers[id]) {
      clearInterval(this.timers[id]);
      delete this.timers[id];
    }
  }
}

export const poller = new Poller();