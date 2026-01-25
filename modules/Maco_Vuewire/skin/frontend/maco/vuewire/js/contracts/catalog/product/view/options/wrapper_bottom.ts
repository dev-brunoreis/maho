import type { ContractId, HtmlString, SlotHtmlMap } from '../../../../shared';

export const CatalogProductViewOptionsWrapperBottomContract =
  'catalog/product_view_options_wrapper_bottom@1' as const satisfies ContractId;

export type CatalogProductViewOptionsWrapperBottomSlotName = 'children';
export type CatalogProductViewOptionsWrapperBottomSlots =
  Partial<Record<CatalogProductViewOptionsWrapperBottomSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewOptionsWrapperBottomPropsV1 {
  slots?: CatalogProductViewOptionsWrapperBottomSlots;
}

