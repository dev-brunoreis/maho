import type { ContractId, HtmlString, SlotHtmlMap } from '../../../../shared';

export const CatalogProductViewOptionsWrapperContract =
  'catalog/product_view_options_wrapper@1' as const satisfies ContractId;

export type CatalogProductViewOptionsWrapperSlotName = 'children';
export type CatalogProductViewOptionsWrapperSlots =
  Partial<Record<CatalogProductViewOptionsWrapperSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewOptionsWrapperPropsV1 {
  hasRequiredOptions: boolean;
  slots?: CatalogProductViewOptionsWrapperSlots;
}

