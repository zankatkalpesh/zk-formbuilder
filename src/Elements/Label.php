<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Illuminate\Support\Arr;
use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Label as LabelContract;
use Zk\FormBuilder\Traits\GeneralMethods;

class Label implements LabelContract
{
    use GeneralMethods;

    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::label';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkLabelElement';

    /** 
     * Label html tag name
     * 
     * @var string
     */
    protected $tagName = 'label';

    /**
     * Label position
     *
     * @var string before-input | after-input | wrapper-before | wrapper-after
     */
    protected $position = 'before-input';

    /**
     * Return new Label instance
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
     * Get label component name
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
     * Get label text before
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
     * Get label text after
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
     * Get label html tag name
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
     * Get label's position
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
        $label = $this->element->getLabelProperty();

        $id = $label['id'] ?? $this->getConfigByKey('id');

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
     * Get the element label text
     * 
     * @return string
     */

    public function getText(): string
    {
        $label = $this->element->getLabelProperty();
        if (is_array($label)) {
            $text = $label['text'] ?? '';
        } else {
            $text = $label;
        }

        if (empty($text)) {
            $text = $this->element->getKey();
        }

        $text = trans($text);

        return $this->toHumanReadable($text);
    }

    /**
     * Get label attributes
     * 
     * @return array
     */
    public function getAttributes(): array
    {
        $labelAttribute = $this->getConfigByKey('attributes') ?? [];

        $attributes = [
            'class' => $this->getConfigByKey('class') ?? '',
        ];

        if (!isset($labelAttribute['for']) || $labelAttribute['for'] === true) {
            $attributes['for'] = $this->getId();
        }
        if (isset($labelAttribute['for']) && $labelAttribute['for'] === false) {
            unset($labelAttribute['for']);
        }

        $attributes = array_merge($labelAttribute, $attributes);

        // Replace the data
        $replaceData = $this->getReplaceData();
        // Add error class
        $replaceData['{errorClass}'] = '';
        if ($this->element->isInvalid()) {
            $errorClass = $this->getConfigByKey('errorClass') ?? '';
            $replaceData['{errorClass}'] = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
        }

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
        // Add error class
        $replaceData['{errorClass}'] = '';
        if ($this->element->isInvalid()) {
            $errorClass = $this->getConfigByKey('wrapper.errorClass') ?? '';
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
        $replaceData['{name}'] = $this->element->getName();
        $replaceData['{id}'] = $this->getId();
        $replaceData['{type}'] = $this->element->getType();

        return $replaceData;
    }

    /**
     * Get label config by key
     * 
     * @param string $key The key to search for within the label configuration.
     * @param string $group The configuration group to search within.
     * @return mixed The configuration value associated with the key, or null if not found.
     */
    protected function getConfigByKey($key, $group = null)
    {
        // Get the configuration directly from the 'field' array 
        $labelProperty = $this->element->getLabelProperty();
        // Try to get the configuration directly from the 'field' array
        $keyConfig = $labelProperty[$key] ?? null;
        if (!empty($keyConfig)) {
            return $keyConfig;
        }
        // If not found in 'field', heck type-specific configuration
        $keyConfig = $this->getConfig('type.' . $this->element->getType() . '.label.' . $key);
        if (!empty($keyConfig)) {
            return $keyConfig;
        }
        // As a last resort, check general 'field.{$group?}.{key}' configuration
        $keyConfig = $this->getConfig('field.label' . ($group ? '.' . $group : '') . '.' . $key);

        return $keyConfig;
    }

    /**
     * Transforms label to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'tag' => $this->getTagName(),
            'text' => $this->getText(),
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
