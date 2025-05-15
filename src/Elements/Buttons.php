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
     * Blade view component path.
     *
     * @var string
     */
    public $component = 'formbuilder::buttons';

    /**
     * JavaScript handler/component name.
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
     * @param WrapperBuilder $wrapperBuilder
     */
    public function __construct(
        protected $form,
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
        $actions = $this->getConfig('actions');
        if (!empty($actions)) {
            foreach ($actions as $name => $action) {
                if (is_numeric($name)) {
                    $name = $action['name'];
                }

                $action['name'] = ($this->form->getPrefix() ? $this->form->getPrefix() . '.' : '') . $name;

                $this->actions[] = app()->makeWith(__NAMESPACE__ . '\\' . 'Action', ['action' => $action, 'form' => $this->form]);
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
        return $this->getConfig('component') ?? $this->component;
    }

    /**
     * Get javascript element
     * 
     * @return string
     */
    public function getJsElement(): string
    {
        return $this->getConfig('jsElement') ?? $this->jsElement;
    }

    /**
     * Get buttons text before
     * 
     * @return mixed
     */
    public function getBefore()
    {
        $before = $this->getConfig('before') ?? '';

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
        $after = $this->getConfig('after') ?? '';

        if (is_callable($after)) {
            $after = call_user_func($after, $this);
        }

        return $after;
    }

    /**
     * Get buttons's position
     *
     * @return string
     */

    public function getPosition(): string
    {
        $position = $this->getConfig('position');

        if (empty($position)) {
            $position = $this->position;
        }

        return $position;
    }

    /**
     * Get wrapper
     *
     * @return array
     */
    public function getWrapper(): array
    {
        $wrapper = $this->getConfig('wrapper') ?? [];

        // Replace the data
        $replaceData = $this->getReplaceData();
        // Add error class
        $replaceData['{errorClass}'] = '';
        if ($this->form->isInvalid()) {
            $errorClass = $this->getConfig('errorClass') ?? '';
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
        $replaceData = $this->getConfig('replace') ?? [];
        $replaceData['{formName}'] = $this->form->getName();
        $replaceData['{formId}'] = $this->form->getId();
        $replaceData['{formPrefix}'] = $this->form->getPrefix();

        return $replaceData;
    }

    /**
     * Get buttons config by key
     * 
     * @param string $key The key to search for within the action configuration.
     * @param string $group The configuration group to search within.
     * @return mixed The configuration value associated with the key, or null if not found.
     */
    public function getConfig($key, $group = null)
    {
        // Try to get the configuration directly from the 'buttons' array
        $keyConfig = Arr::get($this->form->buttons, $key, null);
        if ($keyConfig !== null) {
            return $keyConfig;
        }
        // If not found in 'buttons', heck type-specific configuration
        $keyConfig = $this->form->getConfig('form.buttons.' . $key);
        if ($keyConfig !== null) {
            return $keyConfig;
        }
        // As a last resort, check general 'field.{$group?}.{key}' configuration
        $keyConfig = $this->form->getConfig('form.buttons.' . ($group ? '.' . $group : '') . '.' . $key);

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
