export class OpenwireClient {
  getFormKey() {
    return window.FORM_KEY || window.formKey ||
           document.querySelector('input[name="form_key"]')?.getAttribute('value');
  }

  async call(component, action, payload, state) {
    const updatePayload = {
      id: 'vuewire_' + Date.now(), // Generate unique ID
      component: component,
      calls: [{
        method: action,
        params: [payload] // OpenWire expects params as array
      }],
      updates: state || {}
    };

    // Add form key for CSRF protection
    const formKey = this.getFormKey();
    if (formKey) {
      updatePayload.form_key = formKey;
    }

    const response = await fetch('/openwire/update/index', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(updatePayload)
    });

    return response.json();
  }
}

const client = new OpenwireClient()

export default client