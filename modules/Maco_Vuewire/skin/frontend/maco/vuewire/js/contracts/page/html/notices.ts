import type { ContractId, HtmlString } from '../../shared';

export const PageHtmlNoticesContract =
  'page/html_notices@1' as const satisfies ContractId;

export interface PageHtmlNoticesPropsV1 {
  showDemoNotice: boolean;
  demoNoticeContent: HtmlString;
}
