import type { ContractId, HtmlString } from '../../../../../shared';

export const CatalogProductViewOptionsTypeFileContract =
  'catalog/product_view_options_type_file@1' as const satisfies ContractId;

export interface CatalogProductViewOptionsTypeFilePropsV1 {
  option: {
    id: number;
    title: string;
    isRequired: boolean;
    imageSizeX: number;
    imageSizeY: number;
  };
  formatedPrice?: HtmlString;
  fileInfo?: { title: string } | null;
  fileName: string;
  fieldNameAction: string;
  fieldValueAction: string;
  fileNamed: string;
  sanitizedExtensions?: string;
  maxFileSizeMb?: string | number;
}

