import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { startPolling } from '../src/index';

describe('startPolling', () => {
  beforeEach(() => {
    // Setup DOM
    document.body.innerHTML = '';
    delete (window as any).location;
    (window as any).location = { pathname: '/' };
    vi.clearAllMocks();
    vi.clearAllTimers();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should start polling with interval', async () => {
    vi.useFakeTimers();
    const element = document.createElement('div');
    element.setAttribute('data-ow-id', 'test-id');
    element.setAttribute('data-ow-component', 'test-component');

    const mockFetch = vi.fn().mockResolvedValue({ json: () => ({}) });
    global.fetch = mockFetch;

    startPolling(element, 1000);

    vi.advanceTimersByTime(1000);

    await vi.waitFor(() => {
      expect(mockFetch).toHaveBeenCalledWith('/openwire/update/index', expect.objectContaining({
        body: JSON.stringify({ id: 'test-id', component: 'test-component' })
      }));
    });
  });
});