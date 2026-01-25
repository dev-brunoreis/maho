import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import * as OpenWire from '../src/index';

describe('OpenWire Runtime Integration', () => {
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

  it('should export all expected functions', () => {
    expect(typeof OpenWire.registerOpenWirePlugin).toBe('function');
    expect(typeof OpenWire.registerEffectHandler).toBe('function');
    expect(typeof OpenWire.getFormKey).toBe('function');
    expect(typeof OpenWire.sendUpdate).toBe('function');
    expect(typeof OpenWire.handleResponse).toBe('function');
    expect(typeof OpenWire.patch).toBe('function');
    expect(typeof OpenWire.debounce).toBe('function');
    expect(typeof OpenWire.startPolling).toBe('function');
    expect(typeof OpenWire.bootstrap).toBe('function');
  });

  it('should initialize without errors', () => {
    expect(() => {
      OpenWire.bootstrap();
    }).not.toThrow();
  });
});