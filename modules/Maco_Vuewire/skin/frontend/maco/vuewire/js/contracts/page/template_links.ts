import type { ContractId, HtmlString } from '../shared';

export const PageTemplateLinksContract =
  'page/template_links@1' as const satisfies ContractId;

export interface PageTemplateLinkItem {
  type: 'link';
  label: string;
  url: string;
  title?: string;
  liParams?: string;
  aParams?: string;
  beforeText?: string;
  afterText?: string;
  isFirst?: boolean;
  isLast?: boolean;
}

export interface PageTemplateLinkHtmlItem {
  type: 'html';
  html: HtmlString;
}

export type PageTemplateLinkEntry = PageTemplateLinkItem | PageTemplateLinkHtmlItem;

export interface PageTemplateLinksPropsV1 {
  title?: string | null;
  name?: string | null;
  links: PageTemplateLinkEntry[];
}
