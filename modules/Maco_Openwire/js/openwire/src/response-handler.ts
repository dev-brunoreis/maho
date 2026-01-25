// Response Handler

import type { Response } from './types';
import { DomPatcher } from './dom-patcher';
import { effectManager } from './effect-manager';

export class ResponseHandler {
  constructor(private domPatcher: DomPatcher) {}

  handle(response: Response, element: HTMLElement): void {
    if (response.html) {
      this.domPatcher.patch(element, response.html);
    }
    if (response.state) {
      // Update Alpine state if needed
    }
    if (response.effects) {
      for (const effect of response.effects) {
        effectManager.execute(effect);
      }
    }
  }
}

export const responseHandler = new ResponseHandler(new DomPatcher());