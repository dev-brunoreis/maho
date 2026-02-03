import type { ContractId, HtmlString, SlotHtmlMap } from '../../shared';

export const PageHtmlHeaderContract =
  'page/html_header@1' as const satisfies ContractId;

export type PageHtmlHeaderSlotName =
  | 'logo'
  | 'topSearch'
  | 'welcome'
  | 'topLinks'
  | 'topMenu'
  | 'before'
  | 'after';

export type PageHtmlHeaderSlots =
  Partial<Record<PageHtmlHeaderSlotName, HtmlString>> & SlotHtmlMap;

export interface PageHtmlHeaderPropsV1 {
  baseUrl?: string;
  slots?: PageHtmlHeaderSlots;
}
