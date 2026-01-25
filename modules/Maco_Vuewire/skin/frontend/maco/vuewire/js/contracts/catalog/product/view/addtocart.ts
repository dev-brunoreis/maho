import type { ContractId, HtmlString, SlotHtmlMap } from '../../../shared';

export const CatalogProductViewAddtocartContract =
  'catalog/product_view_addtocart@1' as const satisfies ContractId;

export type CatalogProductViewAddtocartSlotName = 'children';
export type CatalogProductViewAddtocartSlots =
  Partial<Record<CatalogProductViewAddtocartSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewAddtocartPropsV1 {
  buttonTitle: string;
  defaultQty: number;
  isGrouped: boolean;
  isSaleable: boolean;
  slots?: CatalogProductViewAddtocartSlots;
}

