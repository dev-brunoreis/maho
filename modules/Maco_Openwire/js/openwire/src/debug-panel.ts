type Nullable<T> = T | null;

const DEBUG_PANEL_ID = 'openwire-debug-panel';
const DEBUG_STYLE_ID = 'openwire-debug-style';
const DEBUG_ENABLED_CLASS = 'openwire-debug-enabled';
const DEBUG_HIGHLIGHT_STORAGE_KEY = 'openwire.debug.highlight';

// Root markers used across OpenWire versions + raw templates.
// We include the ID-only attributes because some renders may carry IDs even when
// the component name is stored in a sibling attribute (`data-openwire-component`, etc.).
const COMPONENT_ROOT_SELECTOR =
  '[data-ow-component],[data-openwire-component],[data-openwire],[openwire],[data-ow-id],[data-openwire-id]';

const COMPONENT_NAME_SELECTOR =
  '[data-ow-component],[data-openwire-component],[data-openwire],[openwire]';

function safeGetLocalStorage(key: string): Nullable<string> {
  try {
    return window.localStorage?.getItem(key) ?? null;
  } catch {
    return null;
  }
}

function safeSetLocalStorage(key: string, value: string): void {
  try {
    window.localStorage?.setItem(key, value);
  } catch {
    // ignore
  }
}

function getComponentRootElements(): HTMLElement[] {
  // Support both current and legacy/root syntaxes, plus raw `openwire="..."` templates.
  return Array.from(document.querySelectorAll(COMPONENT_ROOT_SELECTOR)) as HTMLElement[];
}

function getComponentNameFromRoot(el: HTMLElement): string {
  return (
    el.getAttribute('data-ow-component') ||
    el.getAttribute('data-openwire-component') ||
    el.getAttribute('data-openwire') ||
    el.getAttribute('openwire') ||
    ''
  );
}

function getComponentIdFromRoot(el: HTMLElement): string {
  return el.getAttribute('data-ow-id') || el.getAttribute('data-openwire-id') || '';
}

function getUiNameFromElement(el: Nullable<HTMLElement>): string {
  const uiEl = (el?.closest?.('[data-ui]') as Nullable<HTMLElement>) || null;
  return uiEl?.getAttribute('data-ui') || '';
}

function getComponentPath(fromEl: Nullable<HTMLElement>): HTMLElement[] {
  const path: HTMLElement[] = [];
  let cur: Nullable<HTMLElement> = fromEl;
  while (cur) {
    if (cur.matches?.(COMPONENT_ROOT_SELECTOR)) {
      path.push(cur);
    }
    cur = cur.parentElement as Nullable<HTMLElement>;
  }
  return path; // nearest-first
}

function rectContainsPoint(rect: DOMRect, x: number, y: number): boolean {
  return x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom;
}

function getDomDepth(el: HTMLElement): number {
  let depth = 0;
  let cur: Nullable<HTMLElement> = el;
  while (cur) {
    depth += 1;
    cur = cur.parentElement as Nullable<HTMLElement>;
  }
  return depth;
}

function uniq<T>(items: T[]): T[] {
  const seen = new Set<T>();
  const out: T[] = [];
  for (const item of items) {
    if (seen.has(item)) continue;
    seen.add(item);
    out.push(item);
  }
  return out;
}

function ensureDebugStyle(): void {
  if (document.getElementById(DEBUG_STYLE_ID)) return;
  const style = document.createElement('style');
  style.id = DEBUG_STYLE_ID;
  style.textContent = `
    #${DEBUG_PANEL_ID} {
      position: fixed;
      right: 12px;
      bottom: 12px;
      z-index: 2147483647;
      max-width: min(360px, calc(100vw - 24px));
      padding: 10px 12px;
      border-radius: 10px;
      background: rgba(17, 24, 39, 0.92);
      color: #fff;
      font: 13px/1.35 ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
      backdrop-filter: blur(8px);
    }

    #${DEBUG_PANEL_ID} .ow-debug__row {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    #${DEBUG_PANEL_ID} .ow-debug__label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      user-select: none;
      cursor: pointer;
    }

    #${DEBUG_PANEL_ID} .ow-debug__checkbox {
      width: 16px;
      height: 16px;
      cursor: pointer;
    }

    #${DEBUG_PANEL_ID} .ow-debug__meta {
      margin-top: 6px;
      color: rgba(255, 255, 255, 0.75);
    }

    #${DEBUG_PANEL_ID} .ow-debug__hover {
      margin-top: 6px;
      color: rgba(255, 255, 255, 0.9);
      white-space: pre-line;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    html.${DEBUG_ENABLED_CLASS} [data-ow-component],
    html.${DEBUG_ENABLED_CLASS} [data-openwire-component],
    html.${DEBUG_ENABLED_CLASS} [data-openwire],
    html.${DEBUG_ENABLED_CLASS} [openwire] {
      position: relative;
      outline: 2px dashed rgba(245, 158, 11, 0.95);
      outline-offset: 2px;
    }

    html.${DEBUG_ENABLED_CLASS} [data-ow-component]::before,
    html.${DEBUG_ENABLED_CLASS} [data-openwire-component]::before,
    html.${DEBUG_ENABLED_CLASS} [data-openwire]::before,
    html.${DEBUG_ENABLED_CLASS} [openwire]::before {
      position: absolute;
      top: 0;
      left: 0;
      z-index: 2147483646;
      max-width: calc(100% - 6px);
      padding: 2px 6px;
      border-radius: 6px;
      background: rgba(245, 158, 11, 0.95);
      color: rgba(17, 24, 39, 1);
      font: 12px/1.35 ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      pointer-events: none;
      content: "";
    }

    html.${DEBUG_ENABLED_CLASS} [data-ow-component]::before { content: attr(data-ow-component); }
    html.${DEBUG_ENABLED_CLASS} [data-openwire-component]:not([data-ow-component])::before { content: attr(data-openwire-component); }
    html.${DEBUG_ENABLED_CLASS} [data-openwire]:not([data-ow-component])::before { content: attr(data-openwire); }
    html.${DEBUG_ENABLED_CLASS} [openwire]:not([data-ow-component]):not([data-openwire])::before { content: attr(openwire); }
  `.trim();
  document.head.appendChild(style);
}

export function initDebugPanel(): void {
  if (typeof document === 'undefined') return;

  // Create the panel once DOM is usable.
  if (document.getElementById(DEBUG_PANEL_ID)) return;
  if (!document.body) return;

  ensureDebugStyle();

  const panel = document.createElement('div');
  panel.id = DEBUG_PANEL_ID;
  panel.setAttribute('role', 'region');
  panel.setAttribute('aria-label', 'OpenWire debug panel');
  panel.innerHTML = `
    <div class="ow-debug__row">
      <label class="ow-debug__label">
        <input class="ow-debug__checkbox" type="checkbox" />
        Highlight OpenWire components
      </label>
    </div>
    <div class="ow-debug__meta"></div>
    <div class="ow-debug__hover"></div>
  `.trim();

  const checkbox = panel.querySelector('input[type="checkbox"]') as Nullable<HTMLInputElement>;
  const meta = panel.querySelector('.ow-debug__meta') as Nullable<HTMLElement>;
  const hover = panel.querySelector('.ow-debug__hover') as Nullable<HTMLElement>;

  const setEnabled = (enabled: boolean) => {
    const root = document.documentElement;
    if (enabled) root.classList.add(DEBUG_ENABLED_CLASS);
    else root.classList.remove(DEBUG_ENABLED_CLASS);
    safeSetLocalStorage(DEBUG_HIGHLIGHT_STORAGE_KEY, enabled ? '1' : '0');
  };

  const refreshMeta = () => {
    if (!meta) return;
    const count = getComponentRootElements().length;
    meta.textContent = `${count} component root${count === 1 ? '' : 's'}`;
  };

  const setHover = (name: string, id: string, ui: string, pathLines: string[]) => {
    if (!hover) return;
    const pathText = pathLines.length ? pathLines.join('\n') : '—';
    hover.textContent =
      `Hover: ${name || '—'}\n` +
      `Id: ${id || '—'}\n` +
      `UI: ${ui || '—'}\n` +
      `Path:\n${pathText}`;
  };

  // Cache component roots so hover can detect nested/sibling roots via geometry.
  let rootsDirty = true;
  let cachedRoots: HTMLElement[] = [];
  const refreshRoots = () => {
    if (!rootsDirty) return;
    cachedRoots = getComponentRootElements();
    rootsDirty = false;
  };

  // Track hovered component name (throttled).
  let hoverRaf = 0;
  let lastHoverKey = '';
  const onPointerMove = (event: Event) => {
    if (hoverRaf) return;
    hoverRaf = window.requestAnimationFrame(() => {
      hoverRaf = 0;
      const ev = event as MouseEvent;
      // Prefer the actual element under the cursor (more reliable than bubble target).
      const underCursor = document.elementFromPoint(ev.clientX, ev.clientY) as Nullable<HTMLElement>;
      const target = (underCursor || (event.target as Nullable<HTMLElement>)) as Nullable<HTMLElement>;

      // Primary strategy: pick from DOM z-order using elementsFromPoint (most accurate when
      // components overlap or the “child” root isn't a DOM ancestor of the hovered node).
      const stack = typeof document.elementsFromPoint === 'function'
        ? (document.elementsFromPoint(ev.clientX, ev.clientY) as HTMLElement[])
        : (target ? [target] : []);

      const rootsFromStack = uniq(
        stack
          .map((el) => (el?.closest?.(COMPONENT_ROOT_SELECTOR) as Nullable<HTMLElement>) || null)
          .filter(Boolean) as HTMLElement[],
      );

      const pickedEl =
        // first element in z-order that maps to a named component root
        stack
          .map((el) => (el?.closest?.(COMPONENT_NAME_SELECTOR) as Nullable<HTMLElement>) || null)
          .find(Boolean) ||
        rootsFromStack[0] ||
        null;

      // Secondary fallback: geometry-based scan (kept for older browsers / edge cases).
      refreshRoots();
      const geomRootsAtPoint = cachedRoots
        .map((el) => ({ el, rect: el.getBoundingClientRect() }))
        .filter(({ rect }) => rectContainsPoint(rect, ev.clientX, ev.clientY))
        .map(({ el, rect }) => ({
          el,
          area: Math.max(0, rect.width) * Math.max(0, rect.height),
          depth: getDomDepth(el),
        }))
        .sort((a, b) => {
          if (a.area !== b.area) return a.area - b.area;
          return b.depth - a.depth;
        })
        .map(({ el }) => el);

      const root = pickedEl || geomRootsAtPoint[0] || (target?.closest?.(COMPONENT_ROOT_SELECTOR) as Nullable<HTMLElement>) || null;

      const name = root ? getComponentNameFromRoot(root) : '';
      const id = root ? getComponentIdFromRoot(root) : '';
      const ui = getUiNameFromElement(root || target);

      // Path: show roots in z-order (most specific first), then fall back to geometry list.
      const pathRoots = rootsFromStack.length ? rootsFromStack : geomRootsAtPoint;
      const pathLines = pathRoots.slice(0, 8).map((el, idx) => {
        const pName = getComponentNameFromRoot(el) || '(unknown component)';
        const pId = getComponentIdFromRoot(el);
        const pUi = getUiNameFromElement(el);
        const parts = [
          `${idx === 0 ? '•' : '↳'} ${pName}`,
          pId ? `#${pId}` : '',
          pUi ? `[${pUi}]` : '',
        ].filter(Boolean);
        return parts.join(' ');
      });

      const key = `${name}__${id}__${ui}__${pathLines.join('|')}`;
      if (key === lastHoverKey) return;
      lastHoverKey = key;
      setHover(name, id, ui, pathLines);
    });
  };

  let observer: Nullable<MutationObserver> = null;
  const ensureObserver = () => {
    if (observer) return;
    let raf = 0;
    observer = new MutationObserver(() => {
      rootsDirty = true;
      if (raf) return;
      raf = window.requestAnimationFrame(() => {
        raf = 0;
        refreshMeta();
      });
    });
    observer.observe(document.body, { subtree: true, childList: true, attributes: true });
  };

  if (checkbox) {
    const initialEnabled = safeGetLocalStorage(DEBUG_HIGHLIGHT_STORAGE_KEY) === '1';
    checkbox.checked = initialEnabled;
    setEnabled(initialEnabled);
    refreshMeta();
    ensureObserver();
    setHover('', '', '', []);

    checkbox.addEventListener('change', () => {
      const enabled = Boolean(checkbox.checked);
      setEnabled(enabled);
      refreshMeta();
      ensureObserver();
    });
  }

  // Always show hovered component name on the panel.
  document.addEventListener('mousemove', onPointerMove, { passive: true });
  document.addEventListener('mouseover', onPointerMove, { passive: true });

  // Keep caches fresh even if highlight is off.
  ensureObserver();

  document.body.appendChild(panel);
}
