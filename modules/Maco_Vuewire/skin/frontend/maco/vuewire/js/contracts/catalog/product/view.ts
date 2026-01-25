import type { ContractId, HtmlString, SlotHtmlMap } from '../../shared';

export const CatalogProductViewContract = 'catalog/product_view@1' as const satisfies ContractId;

export type CatalogProductViewSlotName =
  | 'price'
  | 'tier_price'
  | 'media'
  | 'bundle_prices'
  | 'product_type_availability'
  | 'alert_urls'
  | 'other'
  | 'container1'
  | 'product_type_data'
  | 'extrahint'
  | 'addtocart'
  | 'addto'
  | 'sharing'
  | 'extra_buttons'
  | 'related_products'
  | 'container2'
  | 'upsell_products'
  | 'product_additional_data';

export type CatalogProductViewSlots = Partial<Record<CatalogProductViewSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewPropsV1 {
  /**
   * Canonical product identifier.
   */
  productId: number;

  /**
   * Deprecated alias kept for backwards compatibility (PHTML currently sends `product`).
   */
  product?: number;

  isSaleable: boolean;
  hasOptions: boolean;

  submitUrl: string;

  /**
   * HTML chunks coming from Magento blocks.
   */
  messagesHtml?: HtmlString;
  formKeyHtml?: HtmlString;
  reviewsSummaryHtml?: HtmlString;
  shortDescription?: HtmlString;
  productName?: HtmlString;

  /**
   * Legacy aliases (the preferred path is to use the `price`/`tier_price` slots).
   */
  priceHtml?: HtmlString;
  tierPriceHtml?: HtmlString;

  /**
   * Optional/forward-looking fields (not always provided by PHTML).
   */
  detailedInfoGroup?: Record<string, HtmlString> | null;
  optionsPriceConfig?: unknown | null;

  /**
   * Raw HTML slots extracted from `<slot name="...">...</slot>` in the PHTML.
   */
  slots?: CatalogProductViewSlots;
}

