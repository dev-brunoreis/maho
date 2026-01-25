import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { patch } from '../src/index';

describe('patch', () => {
  beforeEach(() => {
    // Setup DOM
    document.body.innerHTML = '';
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should update data-ow-body content', () => {
    const element = document.createElement('div');
    element.innerHTML = '<div data-ow-body>old</div>';

    patch(element, '<div>new</div>');

    expect(element.querySelector('[data-ow-body]')?.innerHTML).toBe('<div>new</div>');
  });

  it('should update element innerHTML if no data-ow-body', () => {
    const element = document.createElement('div');
    element.innerHTML = 'old';

    patch(element, '<div>new</div>');

    expect(element.innerHTML).toBe('<div>new</div>');
  });
});