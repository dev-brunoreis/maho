import type { ContractId } from '../../../../shared';

export const CatalogProductViewTypeAvailabilityGroupedContract =
  'catalog/product_view_type_availability_grouped@1' as const satisfies ContractId;

export interface CatalogProductViewTypeAvailabilityGroupedPropsV1 {
  isAvailable: boolean;
  hasAssociatedProducts: boolean;
  displayProductStockStatus: boolean;
}

