import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { bootstrap } from '../src/index';

describe('Event Handlers', () => {
  beforeEach(() => {
    // Setup DOM
    document.body.innerHTML = '';
    delete (window as any).location;
    (window as any).location = { pathname: '/' };
    vi.clearAllMocks();
    vi.clearAllTimers();
    bootstrap();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should handle click events', async () => {
    const element = document.createElement('div');
    element.setAttribute('data-openwire', 'counter');
    element.setAttribute('data-openwire-id', 'test-id');
    element.setAttribute('data-openwire-component', 'openwire_component/counter');
    const button = document.createElement('button');
    button.setAttribute('data-openwire:click', 'testMethod');
    element.appendChild(button);
    document.body.appendChild(element);

    const mockFetch = vi.fn().mockResolvedValue({ json: () => ({}) });
    global.fetch = mockFetch;

    button.dispatchEvent(new Event('click', { bubbles: true }));

    await vi.waitFor(() => {
      expect(mockFetch).toHaveBeenCalledWith('/openwire/update/index', expect.objectContaining({
        body: JSON.stringify({ id: 'test-id', component: 'openwire_component/counter', calls: [{ method: 'testMethod', params: [] }] })
      }));
    });
  });

  it('should handle change events', async () => {
    vi.useFakeTimers();
    const element = document.createElement('div');
    element.setAttribute('data-openwire', 'counter');
    element.setAttribute('data-openwire-id', 'test-id');
    element.setAttribute('data-openwire-component', 'openwire_component/counter');
    const input = document.createElement('input');
    input.setAttribute('data-openwire:change', 'testMethod');
    input.value = 'test value';
    element.appendChild(input);
    document.body.appendChild(element);

    const mockFetch = vi.fn().mockResolvedValue({ json: () => ({}) });
    global.fetch = mockFetch;

    input.dispatchEvent(new Event('change', { bubbles: true }));

    vi.advanceTimersByTime(300);

    await vi.waitFor(() => {
      expect(mockFetch).toHaveBeenCalledWith('/openwire/update/index', expect.objectContaining({
        body: JSON.stringify({ id: 'test-id', component: 'openwire_component/counter', calls: [{ method: 'testMethod', params: ['test value'] }] })
      }));
    });
  });

  it('should handle form submit events', async () => {
    const element = document.createElement('div');
    element.setAttribute('data-openwire', 'counter');
    element.setAttribute('data-openwire-id', 'test-id');
    element.setAttribute('data-openwire-component', 'openwire_component/counter');
    const form = document.createElement('form');
    form.setAttribute('data-openwire:submit', 'testMethod');
    const input = document.createElement('input');
    input.name = 'field';
    input.value = 'value';
    form.appendChild(input);
    element.appendChild(form);
    document.body.appendChild(element);

    const mockFetch = vi.fn().mockResolvedValue({ json: () => ({}) });
    global.fetch = mockFetch;

    form.dispatchEvent(new Event('submit', { bubbles: true }));

    await vi.waitFor(() => {
      expect(mockFetch).toHaveBeenCalledWith('/openwire/update/index', expect.objectContaining({
        body: JSON.stringify({ id: 'test-id', component: 'openwire_component/counter', calls: [{ method: 'testMethod', params: [{ field: 'value' }] }] })
      }));
    });
  });
});