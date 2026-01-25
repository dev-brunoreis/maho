import type { ContractId, HtmlString, SlotHtmlMap } from '../../../../shared';

export const CatalogProductViewTypeDefaultContract =
  'catalog/product_view_type_default@1' as const satisfies ContractId;

export type CatalogProductViewTypeDefaultSlotName = 'product_type_data_extra';
export type CatalogProductViewTypeDefaultSlots =
  Partial<Record<CatalogProductViewTypeDefaultSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewTypeDefaultPropsV1 {
  slots?: CatalogProductViewTypeDefaultSlots;
}

