import type { ContractId } from '../../../../../shared';

export const CatalogProductViewOptionsTypeDefaultContract =
  'catalog/product_view_options_type_default@1' as const satisfies ContractId;

export interface CatalogProductViewOptionsTypeDefaultPropsV1 {
  option: {
    id: number;
    title: string;
  };
}

