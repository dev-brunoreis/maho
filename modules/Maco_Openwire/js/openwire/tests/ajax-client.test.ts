import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { sendUpdate } from '../src/index';

describe('sendUpdate', () => {
  beforeEach(() => {
    // Setup DOM
    document.body.innerHTML = '';
    delete (window as any).location;
    (window as any).location = { pathname: '/' };
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should send POST request to correct URL', async () => {
    const mockFetch = vi.fn().mockResolvedValue({
      json: () => Promise.resolve({ html: 'test' })
    });
    global.fetch = mockFetch;

    const payload = { id: 'test', component: 'test-component', calls: [] };
    await sendUpdate(payload);

    expect(mockFetch).toHaveBeenCalledWith('/openwire/update/index', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...payload, form_key: undefined })
    });
  });

  it('should use admin URL when in admin area', async () => {
    const mockFetch = vi.fn().mockResolvedValue({
      json: () => Promise.resolve({})
    });
    global.fetch = mockFetch;

    // Mock admin path
    delete (window as any).location;
    (window as any).location = { pathname: '/admin/test' };

    await sendUpdate({ id: 'test', component: 'test-component' });

    expect(mockFetch).toHaveBeenCalledWith('/admin/openwire/update', expect.any(Object));
  });
});