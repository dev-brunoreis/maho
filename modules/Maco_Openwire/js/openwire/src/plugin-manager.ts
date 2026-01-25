// Plugin Management

export class PluginManager {
  private plugins: any[] = [];

  register(plugin: any): void {
    this.plugins.push(plugin);
  }

  getPlugins(): any[] {
    return [...this.plugins];
  }
}

export const pluginManager = new PluginManager();