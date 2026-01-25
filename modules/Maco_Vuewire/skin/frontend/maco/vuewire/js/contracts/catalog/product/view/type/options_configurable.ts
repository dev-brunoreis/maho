import type { ContractId, HtmlString, SlotHtmlMap } from '../../../../shared';

export const CatalogProductViewTypeOptionsConfigurableContract =
  'catalog/product_view_type_options_configurable@1' as const satisfies ContractId;

export type CatalogProductViewTypeOptionsConfigurableSlotName = 'after';
export type CatalogProductViewTypeOptionsConfigurableSlots =
  Partial<Record<CatalogProductViewTypeOptionsConfigurableSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewTypeOptionsConfigurablePropsV1 {
  attributes: Array<{
    id: number;
    label: string;
  }>;
  jsonConfig: unknown;
  isSaleable: boolean;
  slots?: CatalogProductViewTypeOptionsConfigurableSlots;
}

