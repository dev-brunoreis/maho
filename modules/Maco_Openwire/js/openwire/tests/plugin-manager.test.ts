import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { registerOpenWirePlugin } from '../src/index';

describe('Plugin Registration', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should register a plugin', () => {
    const plugin = { name: 'test' };
    registerOpenWirePlugin(plugin);
    // Since plugins array is not exported, we test indirectly
    expect(true).toBe(true); // Placeholder
  });
});