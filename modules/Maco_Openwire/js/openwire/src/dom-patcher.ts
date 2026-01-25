// DOM Patcher

export class DomPatcher {
  patch(element: HTMLElement, html: string): void {
    const body = element.querySelector('[data-openwire-body]') || element.querySelector('[data-ow-body]');
    if (body) {
      body.innerHTML = html;
    } else {
      // Check if the HTML is a single element with data-openwire or data-ow
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html.trim();
      if (tempDiv.children.length === 1 && (tempDiv.children[0].hasAttribute('data-openwire') || tempDiv.children[0].hasAttribute('data-ow-component'))) {
        const newElement = tempDiv.children[0] as HTMLElement;
        // Update the current element's attributes to match the new element
        Array.from(newElement.attributes).forEach(attr => {
          element.setAttribute(attr.name, attr.value);
        });
        // Remove attributes that are not in the new element
        Array.from(element.attributes).forEach(attr => {
          if (!newElement.hasAttribute(attr.name)) {
            element.removeAttribute(attr.name);
          }
        });
        // Update innerHTML
        element.innerHTML = newElement.innerHTML;
      } else {
        element.innerHTML = html;
      }
    }
  }
}