import type { ContractId } from '../../../shared';

export const CatalogProductViewOptionsContract =
  'catalog/product_view_options@1' as const satisfies ContractId;

export interface CatalogProductViewOptionsPropsV1 {
  options: Array<{ id: number }>;
  jsonConfig: unknown;
}

