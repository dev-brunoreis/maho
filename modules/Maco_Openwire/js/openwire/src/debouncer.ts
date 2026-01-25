// Debouncer

export class Debouncer {
  private timers: { [key: string]: number } = {};

  debounce(func: Function, wait: number, key: string): void {
    clearTimeout(this.timers[key]);
    this.timers[key] = setTimeout(func, wait);
  }

  clear(key: string): void {
    clearTimeout(this.timers[key]);
    delete this.timers[key];
  }
}

export const debouncer = new Debouncer();