# OpenWire: Magento 1 Reactive Components

OpenWire is a Magento 1 module enabling component-based, reactive UI development inspired by Laravel Livewire and Magewire. It allows building dynamic interfaces without full page reloads through AJAX-driven state updates, using Alpine.js for frontend reactivity.

## Architecture Overview

- **Backend**: PHP components as Magento blocks extending `Maco_Openwire_Block_Component_Abstract`, optionally using `Trait_Reactive` and `Trait_Stateful`
- **Frontend**: TypeScript runtime in `js/openwire/src/` with Alpine.js integration, handling events and AJAX to `/openwire/update/index`
- **Communication**: Stateless JSON payloads `{id, component, calls[], initial_state?, props?}` → `{html, state, meta}`
- **State Management**: Non-stateful components pass state explicitly; stateful components persist state in session via `SessionStore`
- **Template Compilation**: Declarative HTML with `@click`, `{{ variables }}`, `openwire="alias"` compiled to operational attributes by `Template_Compiler`

## Development Workflows

- **Build JS**: `npm run build` (Vite bundles to `js/openwire/dist/openwire.js`)
- **Test PHP**: `composer test` (Pest: Unit/Integration/Feature suites, runs both PHP and JS tests)
- **Test JS**: `npm test` (Vitest)
- **Lint**: `composer lint` (php-cs-fixer, phpstan, phpmd)
- **Fix**: `composer fix` (auto-fix style + rector)
- **Dev Server**: `npm run dev` (Vite dev server)

## Conventions

- **Magento Naming**: Classes like `Maco_Openwire_Block_Component_Counter`, PSR-0 autoload from `app/code/local/`
- **Component API**: Extend `Maco_Openwire_Block_Component_Abstract`, use `Trait_Reactive` for aliasing, `Trait_Stateful` for session persistence; `mount($props)`, `hydrate($state)`, public methods (e.g., `increment()`), `_toHtml()` for declarative HTML
- **Allowed Actions**: Define `$_openwireAllowedActions = ['increment']` for security
- **Templates**: Declarative HTML in `_toHtml()` with `@click="method"`, `{{ variable }}`, `openwire="alias"`; compiled to `data-ow:click`, escaped values, `data-ow-component` etc.
- **State**: Hydrate from `initial_state` or session load, dehydrate via `getData()`/`setData()`, persist automatically for stateful
- **AJAX Endpoint**: POST to `/openwire/update/index` with JSON payload
- **Tests**: PSR-4 in `tests/`, Pest syntax (`it()`, `expect()`), bootstrap via `tests/Pest.php`

## Examples

**Component Class** (`app/code/local/Maco/Openwire/Block/Component/Counter.php`):
```php
class Maco_Openwire_Block_Component_Counter extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    protected $_openwireAllowedActions = ['increment', 'decrement'];

    public function mount($props = [])
    {
        parent::mount($props);
        $this->setData('count', $props['count'] ?? 0);
    }

    public function increment()
    {
        $this->setData('count', $this->getData('count') + 1);
    }

    public function decrement()
    {
        $this->setData('count', $this->getData('count') - 1);
    }

    protected function _toHtml()
    {
        $declarativeHtml = '<div openwire="counter">
            <button @click="decrement">-</button>
            <span>{{ count }}</span>
            <button @click="increment">+</button>
        </div>';

        $compiler = Mage::getModel('openwire/template_compiler');
        return $compiler->compile($declarativeHtml, $this);
    }
}
```

**Rendered HTML** (after compilation):
```html
<div data-ow-component="openwire_component/counter" data-ow-id="ow_123" data-ow-config='{"component":"openwire_component/counter","id":"ow_123","stateful":true,"initialState":{"count":0}}' x-data="{}">
    <button data-ow:click="decrement">-</button>
    <span>0</span>
    <button data-ow:click="increment">+</button>
</div>
```

**Test** (`tests/Unit/Model/TemplateCompilerTest.php`):
```php
it('compiles @click directive correctly', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<button @click="increment">+1</button>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('data-ow:click="increment"');
});
```

**JS Event Handling** (from `js/openwire/src/event-handler.ts`):
```typescript
private async handleClick(e: Event): Promise<void> {
    const target = e.target as HTMLElement;
    const method = target.getAttribute('data-ow:click');
    if (method) {
        const element = target.closest('[data-ow-id]') as HTMLElement;
        const id = element?.getAttribute('data-ow-id');
        const component = element?.getAttribute('data-ow-component');
        if (id && component) {
            const payload = { id, component, calls: [{ method, params: [] }] };
            const response = await ajaxClient.sendUpdate(payload);
            responseHandler.handle(response, element);
        }
    }
}
```

Always validate XML configs, ensure action whitelisting, and run full test suites before commits. Use `Trait_Stateful` only when session persistence is required; prefer stateless for better scalability.</content>
<parameter name="filePath">/home/vector/Documents/maco-studios/projects/msantos/modules/Maco_Openwire/.github/copilot-instructions.md