<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Illuminate\Support\Arr;
use Zk\FormBuilder\Contracts\Form;
use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Buttons as ButtonsContract;
use Zk\FormBuilder\Contracts\Action;
use Zk\FormBuilder\Traits\GeneralMethods;

class Buttons implements ButtonsContract
{
    use GeneralMethods;

    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::buttons';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkButtons';

    /**
     * Buttons position
     *
     * @var string form-top | form-bottom
     */
    protected $position = 'form-bottom';

    /**
     * Buttons actions
     * 
     * @var Action[]
     */
    protected $actions = [];

    /**
     * Return new Buttons instance
     *
     * @param Form $form
     * @param string $configPath
     * @param WrapperBuilder $wrapperBuilder
     */
    public function __construct(
        protected $form,
        public $configPath,
        protected WrapperBuilder $wrapperBuilder
    ) {
        $this->makeActions();
    }

    /**
     * Make actions
     * 
     * @return void
     */
    protected function makeActions()
    {
        $actions = $this->getConfigByKey('actions');
        if (!empty($actions)) {
            foreach ($actions as $name => $action) {
                if (is_numeric($name)) {
                    $name = $action['name'];
                }

                $action['name'] = ($this->form->getPrefix() ? $this->form->getPrefix() . '.' : '') . $name;

                $this->actions[] = app()->makeWith(__NAMESPACE__ . '\\' . 'Action', ['action' => $action, 'form' => $this->form, 'configPath' => $this->configPath]);
            }
        }
    }

    /**
     * Get buttons component name
     * 
     * @return string
     */
    public function getComponent(): string
    {
        return $this->getConfigByKey('component') ?? $this->component;
    }

    /**
     * Get javascript element
     * 
     * @return string
     */
    public function getJsElement(): string
    {
        return $this->getConfigByKey('jsElement') ?? $this->jsElement;
    }

    /**
     * Get buttons text before
     * 
     * @return mixed
     */
    public function getBefore()
    {
        $before = $this->getConfigByKey('before') ?? '';

        if (is_callable($before)) {
            $before = call_user_func($before, $this);
        }

        return $before;
    }

    /**
     * Get buttons text after
     * 
     * @return mixed
     */
    public function getAfter()
    {
        $after = $this->getConfigByKey('after') ?? '';

        if (is_callable($after)) {
            $after = call_user_func($after, $this);
        }

        return $after;
    }

    /**
     * Get config
     * 
     * @return mixed
     */
    public function getConfig($key = null)
    {
        $config = config($this->configPath);
        if ($key) {
            return Arr::get($config, $key);
        }

        return $config;
    }

    /**
     * Get buttons's position
     *
     * @return string
     */

    public function getPosition(): string
    {
        $position = $this->getConfigByKey('position');

        if (empty($position)) {
            $position = $this->position;
        }

        return $position;
    }

    /**
     * Check if buttons has position
     *
     * @param string $position
     * @return bool
     */
    public function hasPosition($position): bool
    {
        return $this->getPosition() === $position;
    }

    /**
     * Get wrapper
     *
     * @return array
     */
    public function getWrapper(): array
    {
        $wrapper = $this->getConfigByKey('wrapper') ?? [];

        // Replace the data
        $replaceData = $this->getReplaceData();
        // Add error class
        $replaceData['{errorClass}'] = '';
        if ($this->form->isInvalid()) {
            $errorClass = $this->getConfigByKey('errorClass') ?? '';
            $replaceData['{errorClass}'] = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
        }

        // Wrapper Builder
        $wBuilder = clone $this->wrapperBuilder;
        return $wBuilder
            ->replace($replaceData)
            ->set($wrapper)
            ->build();
    }

    /**
     * Replace Data
     * 
     * @return array
     */
    protected function getReplaceData()
    {
        // Replace the data
        $replaceData = $this->getConfigByKey('replace') ?? [];
        $replaceData['{formName}'] = $this->form->getName();
        $replaceData['{formId}'] = $this->form->getId();
        $replaceData['{formPrefix}'] = $this->form->getPrefix();

        return $replaceData;
    }

    /**
     * Get action config by key
     * 
     * @param string $key The key to search for within the action configuration.
     * @param string $group The configuration group to search within.
     * @return mixed The configuration value associated with the key, or null if not found.
     */
    protected function getConfigByKey($key, $group = null)
    {
        // Get the configuration directly from the 'buttons' array 
        $buttonsProperty = $this->form->buttons;
        // Try to get the configuration directly from the 'buttons' array
        $keyConfig = $buttonsProperty[$key] ?? null;
        if (!empty($keyConfig)) {
            return $keyConfig;
        }
        // If not found in 'buttons', heck type-specific configuration
        $keyConfig = $this->getConfig('form.buttons.' . $key);
        if (!empty($keyConfig)) {
            return $keyConfig;
        }
        // As a last resort, check general 'field.{$group?}.{key}' configuration
        $keyConfig = $this->getConfig('form.buttons.' . ($group ? '.' . $group : '') . '.' . $key);

        return $keyConfig;
    }

    /**
     * Get actions
     * 
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Renders form DOM element
     *
     * @param string $position
     * @param string $view
     * @return mixed
     */
    public function render($position = null, string $view = 'formbuilder::buttons')
    {
        if ($position === null || $this->hasPosition($position)) {
            return view($view, ['buttons' => $this]);
        }

        return '';
    }

    /**
     * Actions to array
     * 
     * @return array
     */
    protected function getActionsToArray(): array
    {
        $actions = [];
        foreach ($this->getActions() as $action) {
            $actions[] = $action->toArray();
        }

        return $actions;
    }

    /**
     * Transforms buttons to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'before' => $this->getBefore(),
            'after' => $this->getAfter(),
            'wrapper' => $this->getWrapper(),
            'position' => $this->getPosition(),
            'component' => $this->getComponent(),
            'actions' => $this->getActionsToArray(),
            'jsElement' => $this->getJsElement(),
        ];
    }
}
