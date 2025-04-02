<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Illuminate\Support\Arr;
use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Error as ErrorContract;
use Zk\FormBuilder\Traits\GeneralMethods;

class Error implements ErrorContract
{
    use GeneralMethods;

    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::error';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkErrorElement';

    /** 
     * Error html tag name
     * 
     * @var string
     */
    protected $tagName = 'span';

    /**
     * Error position
     *
     * @var string before-input | after-input | wrapper-before | wrapper-after
     */
    protected $position = 'after-input';

    /**
     * Return new Error instance
     *
     * @param Element $element
     * @param string $configPath
     * @param WrapperBuilder $wrapperBuilder
     */
    public function __construct(
        protected Element $element,
        public $configPath,
        protected WrapperBuilder $wrapperBuilder
    ) {}

    /**
     * Get error component name
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
     * Get error text before
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
     * Get error text after
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
     * Get error html tag name
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
     * @return array
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
     * Get error's position
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
     * Get the element id
     * 
     * @return string
     */
    public function getId(): string
    {
        $error = $this->element->getErrorProperty();

        $id = $error['id'] ?? $this->getConfigByKey('id');

        if (empty($id)) {
            $id = $this->element->getId();
        }

        $id = $this->toBracketNotation($id);

        return str_replace(
            ['{name}', '{id}'],
            [$this->element->getName(), $this->element->getId()],
            $id
        );
    }

    /**
     * Get the element error text
     * 
     * @return array
     */

    public function getErrors(): array
    {
        return $this->element->getErrors();
    }

    /**
     * Get error attributes
     * 
     * @return array
     */
    public function getAttributes(): array
    {
        $errorAttribute = $this->getConfigByKey('attributes') ?? [];

        $attributes = [
            'class' => $this->getConfigByKey('class') ?? '',
            'id' => $this->getId(),
        ];

        $attributes = array_merge($errorAttribute, $attributes);

        // Replace the data
        $replaceData = $this->getReplaceData();
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
        $replaceData = $this->getConfigByKey('replace') ?? [];
        $replaceData['{name}'] = $this->element->getName();
        $replaceData['{id}'] = $this->getId();
        $replaceData['{type}'] = $this->element->getType();

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
        $replaceData['{name}'] = $this->element->getName();
        $replaceData['{id}'] = $this->getId();
        $replaceData['{type}'] = $this->element->getType();

        return $replaceData;
    }

    /**
     * Get error config by key
     * 
     * @param string $key The key to search for within the error configuration.
     * @param string $group The configuration group to search within.
     * @return mixed The configuration value associated with the key, or null if not found.
     */
    protected function getConfigByKey($key, $group = null)
    {
        // Try to get the configuration directly field the 'getErrorProperty' array
        $keyConfig = Arr::get($this->element->getErrorProperty(), $key, null);
        if ($keyConfig !== null) {
            return $keyConfig;
        }
        // If not found in 'field', heck type-specific configuration
        $keyConfig = $this->getConfig('type.' . $this->element->getType() . '.error.' . $key);
        if ($keyConfig !== null) {
            return $keyConfig;
        }
        // As a last resort, check general 'field.{$group?}.{key}' configuration
        $keyConfig = $this->getConfig('field.error' . ($group ? '.' . $group : '') . '.' . $key);

        return $keyConfig;
    }

    /**
     * Transforms error to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'tag' => $this->getTagName(),
            'errors' => $this->getErrors(),
            'before' => $this->getBefore(),
            'after' => $this->getAfter(),
            'attributes' => $this->getAttributes(),
            'wrapper' => $this->getWrapper(),
            'position' => $this->getPosition(),
            'component' => $this->getComponent(),
            'jsElement' => $this->getJsElement(),
        ];
    }
}
