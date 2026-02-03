import type { ContractId, HtmlString, SlotHtmlMap } from '../../shared';

export const PageHtmlFooterContract =
  'page/html_footer@1' as const satisfies ContractId;

export type PageHtmlFooterSlotName = 'content';

export type PageHtmlFooterSlots =
  Partial<Record<PageHtmlFooterSlotName, HtmlString>> & SlotHtmlMap;

export interface PageHtmlFooterPropsV1 {
  copyright?: string;
  slots?: PageHtmlFooterSlots;
}
