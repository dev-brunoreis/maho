import type { ContractId, HtmlString } from '../../../../../shared';

export const CatalogProductViewOptionsTypeTextContract =
  'catalog/product_view_options_type_text@1' as const satisfies ContractId;

export interface CatalogProductViewOptionsTypeTextPropsV1 {
  option: {
    id: number;
    title: string;
    isRequired: boolean;
    type: string;
    maxCharacters?: number | null;
    defaultValue?: string;
  };
  formatedPrice?: HtmlString;
}

