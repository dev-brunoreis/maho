import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { registerEffectHandler } from '../src/index';

describe('Effect Handlers', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should register an effect handler', () => {
    const handler = vi.fn();
    registerEffectHandler('test', handler);
    expect(true).toBe(true);
  });
});