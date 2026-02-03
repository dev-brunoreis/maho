import type { ContractId, HtmlString, SlotHtmlMap } from '../../shared';

export const CatalogsearchFormMiniContract =
  'catalogsearch/form_mini@1' as const satisfies ContractId;

export type CatalogsearchFormMiniSlotName = 'searchButtonContent' | 'after';

export type CatalogsearchFormMiniSlots =
  Partial<Record<CatalogsearchFormMiniSlotName, HtmlString>> & SlotHtmlMap;

export interface CatalogsearchFormMiniPropsV1 {
  resultUrl: string;
  queryParamName: string;
  queryText: string;
  maxQueryLength: number;
  placeholder: string;
  searchButtonTitle: string;
  suggestUrl: string;
  slots?: CatalogsearchFormMiniSlots;
}
