import type { ContractId, HtmlString, SlotHtmlMap } from '../../../../shared';

export const CatalogProductViewTypeGroupedContract =
  'catalog/product_view_type_grouped@1' as const satisfies ContractId;

export type CatalogProductViewTypeGroupedSlotName = 'product_type_data_extra';
export type CatalogProductViewTypeGroupedSlots =
  Partial<Record<CatalogProductViewTypeGroupedSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewTypeGroupedPropsV1 {
  isSaleable: boolean;
  hasAssociatedProducts: boolean;
  canShowProductPrice: boolean;
  associatedProducts: Array<{
    id: number;
    name: string;
    is_saleable: boolean;
    qty: number;
    price_html: HtmlString;
    tier_price_html: HtmlString;
    thumbnail: string;
    thumbnail2x: string;
    image_label: string;
  }>;
  slots?: CatalogProductViewTypeGroupedSlots;
}

