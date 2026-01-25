import type { ContractId } from '../../../../shared';

export const CatalogProductViewTypeAvailabilityDefaultContract =
  'catalog/product_view_type_availability_default@1' as const satisfies ContractId;

export interface CatalogProductViewTypeAvailabilityDefaultPropsV1 {
  isAvailable: boolean;
  displayProductStockStatus: boolean;
}

