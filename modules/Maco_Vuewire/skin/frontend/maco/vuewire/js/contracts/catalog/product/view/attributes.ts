import type { ContractId, HtmlString } from '../../../shared';

export const CatalogProductViewAttributesContract =
  'catalog/product_view_attributes@1' as const satisfies ContractId;

export interface CatalogProductViewAttributesPropsV1 {
  additionalData: Array<{
    label: string;
    value: HtmlString;
    code: string;
  }>;
}

