import type { ContractId, HtmlString } from '../../../shared';

export const CatalogProductViewDescriptionContract =
  'catalog/product_view_description@1' as const satisfies ContractId;

export interface CatalogProductViewDescriptionPropsV1 {
  description?: HtmlString;
}

