// OpenWire Type Definitions

export interface Call {
  method: string;
  params: any[];
}

export interface UpdatePayload {
  id?: string;
  calls?: Call[];
  updates?: any;
  form_key?: string;
  server_class?: string;
  initial_state?: any;
}

export interface Effect {
  type: string;
  data?: any;
}

export interface Response {
  html?: string;
  state?: any;
  effects?: Effect[];
}

export interface ComponentConfig {
  component?: string;
  id?: string;
  stateful?: boolean;
  pollIntervalMs?: number;
  initialState?: any;
}