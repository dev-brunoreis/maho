import type { ContractId, HtmlString, SlotHtmlMap } from '../../shared';

export const NewsletterSubscribeContract =
  'newsletter/subscribe@1' as const satisfies ContractId;

export type NewsletterSubscribeSlotName = 'formKey';

export type NewsletterSubscribeSlots =
  Partial<Record<NewsletterSubscribeSlotName, HtmlString>> & SlotHtmlMap;

export interface NewsletterSubscribePropsV1 {
  formActionUrl: string;
  slots?: NewsletterSubscribeSlots;
}
