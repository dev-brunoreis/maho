import type { ContractId } from '../../shared';

export const PageHtmlTitleContract =
  'page/html_title@1' as const satisfies ContractId;

export interface PageHtmlTitlePropsV1 {
  title: string;
}
