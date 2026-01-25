import type { ContractId, HtmlString } from '../../../shared';

export const CatalogProductViewPriceCloneContract =
  'catalog/product_view_price_clone@1' as const satisfies ContractId;

export interface CatalogProductViewPriceClonePropsV1 {
  priceHtml: HtmlString;
}

