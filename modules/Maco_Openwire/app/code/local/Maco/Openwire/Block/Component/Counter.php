<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Counter Component.
 *
 * A simple example component demonstrating OpenWire's reactive capabilities.
 * Provides increment and decrement functionality with state persistence.
 * The component renders a counter with buttons to modify the count value.
 *
 * @package Maco_Openwire_Block_Component
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Block_Component_Counter extends Maco_Openwire_Block_Component_Abstract implements Maco_Openwire_Block_Component_Contracts_DataProvider
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    /**
     * @var array
     */
    protected $openwireAllowedActions = [
        Maco_Openwire_Block_Component_CounterActionEnum::INCREMENT->value,
        Maco_Openwire_Block_Component_CounterActionEnum::DECREMENT->value,
    ];

    /**
     * Mount the counter component.
     *
     * Initializes the component with props, setting the initial count value.
     *
     * @param array $props Component properties, expects 'count' key
     * @return void
     */
    public function mount($props = [])
    {
        parent::mount($props);
        $this->setData('count', $props['count'] ?? 0);
    }

    /**
     * Increment the counter value.
     *
     * Increases the count by 1 and updates the component state.
     *
     * @return void
     */
    public function increment()
    {
        $count = $this->getData('count') + 1;
        $this->setData('count', $count);
    }

    /**
     * Decrement the counter value.
     *
     * Decreases the count by 1 and updates the component state.
     *
     * @return void
     */
    public function decrement()
    {
        $count = $this->getData('count') - 1;
        $this->setData('count', $count);
    }

    /**
     * Render the counter HTML.
     *
     * Returns declarative HTML with OpenWire directives that get compiled
     * into reactive Alpine.js attributes for client-side interaction.
     *
     * @return string Compiled HTML output
     */
    protected function _toHtml()
    {
        $declarativeHtml = '<div openwire="counter">
        <button @click="decrement">-</button>
        <span>{{ count }}</span>
        <button @click="increment">+</button>
    </div>';

        // Compile the declarative HTML
        /** @var Maco_Openwire_Model_Template_Compiler $compiler */
        $compiler = Mage::getModel('openwire/template_compiler');
        return $compiler->compile($declarativeHtml, $this);
    }

    /**
     * Get data payload for client-driven rendering.
     *
     * @return array
     */
    public function getDataPayload(): array
    {
        return [
            'count' => $this->getData('count'),
            'actions' => [
                'increment' => true,
                'decrement' => true,
            ],
        ];
    }

    /**
     * Get HTML payload (optional for data mode).
     *
     * @return string|null
     */
    public function getHtmlPayload(): ?string
    {
        return $this->_toHtml();
    }
}
