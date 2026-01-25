import type { ContractId, HtmlString } from '../../../../../shared';

export const CatalogProductViewOptionsTypeSelectContract =
  'catalog/product_view_options_type_select@1' as const satisfies ContractId;

export interface CatalogProductViewOptionsTypeSelectPropsV1 {
  option: {
    id: number;
    title: string;
    isRequired: boolean;
    type: string;
  };
  valuesHtml: HtmlString;
}

