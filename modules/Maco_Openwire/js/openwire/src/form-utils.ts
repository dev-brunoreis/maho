// Form Utilities

export class FormUtils {
  static getFormKey(): string | undefined {
    return (window as any).FORM_KEY ||
           (window as any).formKey ||
           document.querySelector('input[name="form_key"]')?.getAttribute('value');
  }
}