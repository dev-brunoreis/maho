import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { getFormKey } from '../src/index';

describe('getFormKey', () => {
  beforeEach(() => {
    // Setup DOM
    document.body.innerHTML = '';
    delete (window as any).FORM_KEY;
    delete (window as any).formKey;
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('should return form key from window.FORM_KEY', () => {
    (window as any).FORM_KEY = 'test_key';
    expect(getFormKey()).toBe('test_key');
  });

  it('should return form key from window.formKey', () => {
    delete (window as any).FORM_KEY;
    (window as any).formKey = 'test_key2';
    expect(getFormKey()).toBe('test_key2');
  });

  it('should return form key from DOM input', () => {
    delete (window as any).FORM_KEY;
    delete (window as any).formKey;
    document.body.innerHTML = '<input name="form_key" value="dom_key" />';
    expect(getFormKey()).toBe('dom_key');
  });

  it('should return undefined if no form key found', () => {
    delete (window as any).FORM_KEY;
    delete (window as any).formKey;
    document.body.innerHTML = '';
    expect(getFormKey()).toBeUndefined();
  });
});