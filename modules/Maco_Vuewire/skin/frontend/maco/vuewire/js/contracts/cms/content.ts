import type { ContractId, HtmlString } from '../../shared';

export const CmsContentContract =
  'cms/content@1' as const satisfies ContractId;

export interface CmsContentPropsV1 {
  pageContent: HtmlString;
}
