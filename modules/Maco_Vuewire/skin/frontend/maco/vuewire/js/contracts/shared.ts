/**
 * Shared contract primitives for Vuewire mountpoints.
 *
 * The source of truth is always the `.phtml` mountpoint:
 * - `data-props` is JSON-serialized and becomes component props.
 * - `<slot name="...">...</slot>` children are captured as raw HTML strings.
 */

export type HtmlString = string;

/**
 * Raw slot HTML extracted from `<slot name="...">...</slot>` in the PHTML.
 * Keys are slot names, values are HTML strings.
 */
export type SlotHtmlMap = Record<string, HtmlString>;

/**
 * A mountpoint contract identifier, recommended to be versioned.
 * Example: `catalog/product_view@1`
 */
export type ContractId = `${string}@${number}`;

