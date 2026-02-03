import type { ContractId, HtmlString } from '../../shared';

export const PageHtmlTopmenuContract =
  'page/html_topmenu@1' as const satisfies ContractId;

export interface PageHtmlTopmenuPropsV1 {
  menuHtml: HtmlString;
}
