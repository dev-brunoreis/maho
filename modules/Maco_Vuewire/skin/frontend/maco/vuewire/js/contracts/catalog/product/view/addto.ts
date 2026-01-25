import type { ContractId } from '../../../shared';

export const CatalogProductViewAddtoContract = 'catalog/product_view_addto@1' as const satisfies ContractId;

export interface CatalogProductViewAddtoPropsV1 {
  wishlistSubmitUrl?: string;
  compareUrl?: string;
  formKey?: string;
}

