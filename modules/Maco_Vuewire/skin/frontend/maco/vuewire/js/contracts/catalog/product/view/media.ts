import type { ContractId, HtmlString, SlotHtmlMap } from '../../../shared';

export const CatalogProductViewMediaContract = 'catalog/product_view_media@1' as const satisfies ContractId;

export type CatalogProductViewMediaSlotName = 'after';
export type CatalogProductViewMediaSlots = Partial<Record<CatalogProductViewMediaSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogProductViewMediaPropsV1 {
  mainImage: string;
  mainImageLabel: string;
  mainImageWidth: number;
  mainImageHeight: number;
  galleryImages: Array<{
    url: string;
    label: string;
    thumbnail: string;
    thumbnail2x: string;
    visible: boolean;
  }>;
  slots?: CatalogProductViewMediaSlots;
}

