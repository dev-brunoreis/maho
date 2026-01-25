/// <reference types="vite/client" />

declare module '*.vue' {
  import type { DefineComponent } from 'vue';
  const component: DefineComponent<{}, {}, any>;
  export default component;
}

declare global {
  interface Window {
    Product?: any;
    VarienForm?: any;
    Validation?: any;
    optionsPrice?: any;
    opConfig?: any;
    spConfig?: any;
    dateOption?: any;
    productAddToCartForm?: any;
    minicart?: any;
    customFormSubmit?: any;
    Translator?: { translate?: (s: string) => string };
    validateOptionsCallback?: (elmId: string, result: string) => void;
    __VUEWIRE_DEV__?: boolean;
  }
}

export {}
