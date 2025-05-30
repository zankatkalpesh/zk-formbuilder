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
     * Blade view component path.
     *
     * @var string
     */
    public $component = 'formbuilder::label';

    /**
     * JavaScript handler/component name.
     *
     * @var string
     */
    public $jsElement = 'ZkLabel';

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
     * @param WrapperBuilder $wrapperBuilder
     */
    public function __construct(
        protected Element $element,
        protected WrapperBuilder $wrapperBuilder
    ) {}

    /**
     * Get label component name
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
     * Get label text before
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
     * Get label text after
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
     * Get label html tag name
     * 
     * @return string
     */
    public function getTagName(): string
    {
        $tag = $this->getConfig('tag');

        if (empty($tag)) {
            $tag = $this->tagName;
        }

        return $tag;
    }

    /**
     * Get label's position
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
     * Get the element id
     * 
     * @return string
     */
    public function getId(): string
    {
        $label = $this->element->getLabelProperty();

        $id = $label['id'] ?? $this->getConfig('id');

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
        $labelAttribute = $this->getConfig('attributes') ?? [];

        $attributes = [
            'class' => $this->getConfig('class') ?? '',
        ];

        if ($this->element->isRequired('frontend')) {
            $attributes['class'] .= ' ' . ($this->getConfig('requiredClass') ?? 'required');
        }

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
            $errorClass = $this->getConfig('errorClass') ?? '';
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
        $wrapper = $this->getConfig('wrapper') ?? [];

        // Replace the data
        $replaceData = $this->getReplaceData();
        // Add error class
        $replaceData['{errorClass}'] = '';
        if ($this->element->isInvalid()) {
            $errorClass = $this->getConfig('wrapper.errorClass') ?? '';
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
    protected function getConfig($key, $group = null)
    {
        // Try to get the configuration directly field the 'getLabelProperty' array
        $keyConfig = Arr::get($this->element->getLabelProperty(), $key, null);
        if ($keyConfig !== null) {
            return $keyConfig;
        }
        // If not found in 'field', heck type-specific configuration
        $keyConfig = $this->element->getForm()->getConfig('type.' . $this->element->getType() . '.label.' . $key);
        if ($keyConfig !== null) {
            return $keyConfig;
        }
        // As a last resort, check general 'field.{$group?}.{key}' configuration
        $keyConfig = $this->element->getForm()->getConfig('field.label' . ($group ? '.' . $group : '') . '.' . $key);

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
