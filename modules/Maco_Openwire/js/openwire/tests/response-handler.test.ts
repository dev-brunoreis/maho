import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { handleResponse, registerEffectHandler } from '../src/index';

describe('handleResponse', () => {
  beforeEach(() => {
    // Setup DOM
    document.body.innerHTML = '';
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should patch element with html', () => {
    const element = document.createElement('div');
    element.innerHTML = '<div data-ow-body>old</div>';
    document.body.appendChild(element);

    handleResponse({ html: '<div>new</div>' }, element);

    expect(element.querySelector('[data-ow-body]')?.innerHTML).toBe('<div>new</div>');
  });

  it('should call effect handlers', () => {
    const handler = vi.fn();
    registerEffectHandler('test', handler);

    const element = document.createElement('div');
    handleResponse({ effects: [{ type: 'test', data: 'testData' }] }, element);

    expect(handler).toHaveBeenCalledWith('testData');
  });
});