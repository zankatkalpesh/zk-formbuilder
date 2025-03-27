<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Illuminate\Support\Arr;
use Zk\FormBuilder\Contracts\Form;
use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Action as ActionContract;
use Zk\FormBuilder\Traits\GeneralMethods;

class Action implements ActionContract
{
    use GeneralMethods;

    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::template.action';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkAction';

    /** 
     * Action html tag name
     * 
     * @var string
     */
    protected $tagName = 'button';

    /**
     * Element action
     *
     * @var array
     */
    public $action;

    /**
     * Return new Action instance
     *
     * @param array $action
     * @param Form $form
     * @param string $configPath
     * @param WrapperBuilder $wrapperBuilder
     */
    public function __construct(
        $action,
        protected $form,
        public $configPath,
        protected WrapperBuilder $wrapperBuilder
    ) {
        $this->action = $action;
    }

    /**
     * Get action component name
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
     * Get action text before
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
     * Get action text after
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
     * Get action html tag name
     * 
     * @return string
     */
    public function getTagName(): string
    {
        $tag = $this->getConfigByKey('tag');

        if (empty($tag)) {
            $tag = $this->tagName;
        }

        return $tag;
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
     * Get the element name
     * 
     * @return string
     */
    public function getName(): string
    {
        $name = $this->action['name'] ?? '';
        $name = $this->toBracketNotation($name);

        // Replace the data
        $replaceData = $this->getConfigByKey('replace') ?? [];
        $replaceData['{formName}'] = $this->form->getName();
        $replaceData['{formId}'] = $this->form->getId();
        $replaceData['{formPrefix}'] = $this->form->getPrefix();

        return $name;
    }

    /**
     * Get the element id
     * 
     * @return string
     */
    public function getId(): string
    {
        $id = $this->action['id'] ?? '';
        if (empty($id)) {
            $id = $this->getName();
        }
        $id = $this->toBracketNotation($id);

        // Replace the data
        $replaceData = $this->getConfigByKey('replace') ?? [];
        $replaceData['{formName}'] = $this->form->getName();
        $replaceData['{formId}'] = $this->form->getId();
        $replaceData['{formPrefix}'] = $this->form->getPrefix();

        return $id;
    }

    /**
     * Get the element action text
     * 
     * @return string
     */

    public function getText(): string
    {
        $text = $this->action['text'] ?? '';
        if (empty($text)) {
            $text = $this->getName();
        }

        return $this->toHumanReadable($text);
    }

    /**
     * Get action attributes
     * 
     * @return array
     */
    public function getAttributes(): array
    {
        $actionAttribute = $this->getConfigByKey('attributes') ?? [];

        $attributes = [
            'class' => $this->getConfigByKey('class') ?? '',
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];

        $attributes = array_merge($actionAttribute, $attributes);
        // Add error class
        $replaceData['{errorClass}'] = '';
        if ($this->form->isInvalid()) {
            $errorClass = $this->getConfigByKey('errorClass') ?? '';
            $replaceData['{errorClass}'] = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
        }
        // Replace the data
        $attributes = $this->replacePattern($attributes, $replaceData);

        return $attributes;
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
        $replaceData['{id}'] = $this->getId();
        $replaceData['{name}'] = $this->getName();
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
        // Get the configuration directly from the 'action' array 
        $actionProperty = $this->action;
        // Try to get the configuration directly from the 'action' array
        $keyConfig = $actionProperty[$key] ?? null;
        if (!empty($keyConfig)) {
            return $keyConfig;
        }

        // If not found in 'buttons', heck type-specific configuration
        $keyConfig = $this->getConfig('form.buttons.actionConfig.' . $key);

        return $keyConfig;
    }

    /**
     * Transforms action to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'tag' => $this->getTagName(),
            'text' => $this->getText(),
            'before' => $this->getBefore(),
            'after' => $this->getAfter(),
            'attributes' => $this->getAttributes(),
            'wrapper' => $this->getWrapper(),
            'component' => $this->getComponent(),
            'jsElement' => $this->getJsElement(),
        ];
    }
}
