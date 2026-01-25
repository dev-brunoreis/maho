// AJAX Client

import type { UpdatePayload, Response } from './types';
import { FormUtils } from './form-utils';

export class AjaxClient {
  async sendUpdate(payload: UpdatePayload): Promise<Response> {
    const formKey = FormUtils.getFormKey();
    if (formKey) {
      payload.form_key = formKey;
    }
    const isAdmin = window.location.pathname.startsWith('/admin');
    const url = isAdmin ? '/admin/openwire/update' : '/openwire/update/index';
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(payload),
    });
    return response.json();
  }
}

export const ajaxClient = new AjaxClient();