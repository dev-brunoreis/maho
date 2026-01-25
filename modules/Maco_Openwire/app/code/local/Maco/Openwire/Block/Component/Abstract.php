<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Abstract base class for OpenWire components.
 *
 * This class provides the foundational structure for creating reactive, component-based UI elements
 * in Magento 1, inspired by Laravel Livewire. It handles state management, action execution,
 * and rendering of declarative HTML templates that are compiled into operational attributes.
 *
 * Components extending this class can be stateful or stateless, reactive or polling-based,
 * and support AJAX-driven updates without full page reloads.
 *
 * @abstract
 * @package Maco_Openwire_Block_Component
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
abstract class Maco_Openwire_Block_Component_Abstract extends Mage_Core_Block_Template
{
    /**
     * Allowed actions
     *
     * @var array
     */
    protected $openwireAllowedActions = [];

    /**
     * Component ID
     *
     * @var string
     */
    protected $componentId;

    /**
     * Props
     *
     * @var array
     */
    protected $props = [];

    /**
     * Constructor.
     *
     * Initializes the component with a unique ID for identification in AJAX requests.
     */
    public function __construct()
    {
        parent::__construct();
        $this->componentId = uniqid('ow_');
    }

    /**
     * Get component ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->componentId;
    }

    /**
     * Set component ID
     *
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->componentId = $id;
    }

    /**
     * Mount the component
     *
     * @param array $props
     * @return void
     */
    public function mount(array $props = [])
    {
        $this->props = $props;
    }

    /**
     * Hydrate state
     *
     * @param array $state
     * @return void
     */
    public function hydrate(array $state)
    {
        $this->setData($state);
    }

    /**
     * Dehydrate state
     *
     * @return array
     */
    public function dehydrate()
    {
        return $this->getData();
    }

    /**
     * Render payload
     *
     * @return array
     */
    public function renderPayload()
    {
        return [
            'html' => $this->toHtml(),
            'state' => $this->dehydrate(),
            'meta' => [
                'pollIntervalMs' => $this->getPollIntervalMs(),
            ],
        ];
    }

    /**
     * Render HTML (to be overridden by child classes)
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }

    /**
     * Get OpenWire configuration for the component
     *
     * @return array
     */
    public function getOpenwireConfig()
    {
        $config = [
            'component' => $this->getComponentAlias(),
            'id' => $this->getId(),
            'stateful' => $this->isStateful(),
            'pollIntervalMs' => $this->getPollIntervalMs(),
        ];

        if ($this->isStateful()) {
            $config['initialState'] = $this->dehydrate();
        }

        return $config;
    }

    /**
     * Execute action
     *
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function executeAction($method, $params = [])
    {
        if (!in_array($method, $this->openwireAllowedActions)) {
            throw new Exception(sprintf("Action '%s' not allowed", $method));
        }

        if (!method_exists($this, $method)) {
            throw new Exception(sprintf("Method '%s' does not exist", $method));
        }

        return call_user_func_array([$this, $method], $params);
    }

    /**
     * Get poll interval (default null)
     *
     * @return int|null
     */
    protected function getPollIntervalMs()
    {
        return null;
    }

    /**
     * Check if stateful (default false)
     *
     * @return bool
     */
    protected function isStateful()
    {
        return false;
    }

    /**
     * Get allowed actions
     *
     * @return array
     */
    public function getAllowedActions()
    {
        return $this->openwireAllowedActions;
    }

    /**
     * Get default render mode for this component.
     *
     * Components can override this to default to 'data' mode.
     * Default is null, which falls back to global default.
     *
     * @return string|null 'html', 'data', or null
     */
    public function getDefaultMode(): ?string
    {
        return null;
    }

    /**
     * Generate OpenWire root element
     *
     * @param string $alias
     * @param array $attrs
     * @return string
     */
    protected function openwireRoot($alias, array $attrs = [])
    {
        $config = htmlspecialchars(json_encode($this->getOpenwireConfig()), ENT_QUOTES);
        $attrString = $this->renderHtmlAttributes($attrs);

        return sprintf(
            '<div data-openwire="%s" data-openwire-id="%s" data-openwire-config="%s"%s>',
            $alias,
            $this->getId(),
            $config,
            $attrString
        );
    }

    /**
     * Render HTML attributes
     *
     * @param array $attrs
     * @return string
     */
    protected function renderHtmlAttributes(array $attrs)
    {
        $result = '';
        foreach ($attrs as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $result .= ' ' . $name;
                }
            } else {
                $result .= ' ' . $name . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            }
        }
        return $result;
    }
}
