import type { ContractId, HtmlString, SlotHtmlMap } from '../../shared';

export const PageHtmlBreadcrumbsContract =
  'page/html_breadcrumbs@1' as const satisfies ContractId;

export interface BreadcrumbCrumb {
  label: string;
  link: string | null;
}

export type PageHtmlBreadcrumbsSlotName = 'before' | 'after';

export type PageHtmlBreadcrumbsSlots =
  Partial<Record<PageHtmlBreadcrumbsSlotName, HtmlString>> & SlotHtmlMap;

export interface PageHtmlBreadcrumbsPropsV1 {
  crumbs: BreadcrumbCrumb[];
  slots?: PageHtmlBreadcrumbsSlots;
}
