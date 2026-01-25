import type { ContractId, HtmlString, SlotHtmlMap } from '../../../shared';

export const CatalogProductViewAdditionalContract =
  'catalog/product_view_additional@1' as const satisfies ContractId;

export type CatalogProductViewAdditionalSlotName = 'children';
export type CatalogProductViewAdditionalSlots =
  Partial<Record<CatalogProductViewAdditionalSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewAdditionalPropsV1 {
  slots?: CatalogProductViewAdditionalSlots;
}

