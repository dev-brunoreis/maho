// Effect Handler Management

import type { Effect } from './types';

export class EffectManager {
  private handlers: { [key: string]: Function } = {};

  register(type: string, handler: Function): void {
    this.handlers[type] = handler;
  }

  execute(effect: Effect): void {
    const handler = this.handlers[effect.type];
    if (handler) {
      handler(effect.data);
    }
  }
}

export const effectManager = new EffectManager();