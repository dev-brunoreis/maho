import type { ContractId } from '../../../shared';

export const CatalogProductViewTierpricesContract =
  'catalog/product_view_tierprices@1' as const satisfies ContractId;

/**
 * Tier price payload is passed through from Magento.
 * The exact shape varies by Magento/Maho version and tax settings,
 * so we keep it permissive but documented.
 */
export type TierPriceLike = Record<string, any>;

export interface CatalogProductViewTierpricesPropsV1 {
  tierPrices: TierPriceLike[];
  inGrouped: boolean;

  // Tax/weee/context flags used to format tier price display
  finalPriceInclTax: number;
  weeeTaxAmount: number;
  weeeTaxAttributes?: Array<{
    name: string;
    amount: number;
    taxAmount: number;
  }>;
  displayBothPrices: boolean;
  displayPriceIncludingTax: boolean;
  weeeDisplayType: number;
  canApplyMsrp: boolean;
  isShowPriceOnGesture: boolean;
}

