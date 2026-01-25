import type { ContractId, HtmlString } from '../../../../../shared';

export const CatalogProductViewOptionsTypeDateContract =
  'catalog/product_view_options_type_date@1' as const satisfies ContractId;

export interface CatalogProductViewOptionsTypeDatePropsV1 {
  option: {
    id: number;
    title: string;
    isRequired: boolean;
    type: string;
  };
  formatedPrice?: HtmlString;
  dateHtml?: HtmlString;
  timeHtml?: HtmlString;
}

