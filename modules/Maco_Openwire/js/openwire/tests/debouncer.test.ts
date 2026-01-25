import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { debounce } from '../src/index';

describe('debounce', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    vi.clearAllTimers();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should delay function execution', async () => {
    vi.useFakeTimers();
    const func = vi.fn();

    debounce(func, 100, 'test');
    expect(func).not.toHaveBeenCalled();

    vi.advanceTimersByTime(100);
    expect(func).toHaveBeenCalled();
  });

  it('should cancel previous call', () => {
    vi.useFakeTimers();
    const func = vi.fn();

    debounce(func, 100, 'test');
    debounce(func, 100, 'test');

    vi.advanceTimersByTime(100);
    expect(func).toHaveBeenCalledTimes(1);
  });
});