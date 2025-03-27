<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Zk\FormBuilder\Helpers\WrapperBuilder;

class SelectElement extends Element
{
    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::template.select';

    /**
     * Element type
     *
     * @var string
     */
    public $elementType = 'select';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkSelectElement';

    /**
     * Element options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Return new Element instance
     *
     * @param array $field
     * @param Element | Form $parent
     * @param array $properties
     * @param string $configPath
     * @param Factory $elementFactory
     * @param WrapperBuilder $wrapperBuilder
     */
    public function __construct(
        $field,
        protected $parent,
        $properties,
        $configPath,
        protected Factory $elementFactory,
        protected WrapperBuilder $wrapperBuilder
    ) {
        parent::__construct($field, $parent, $properties, $configPath, $elementFactory, $wrapperBuilder);

        $this->setOptions();
    }

    /**
     * Set options
     *
     * @return void
     */
    protected function setOptions(): void
    {
        $options = $this->field['options'] ?? [];

        if (is_callable($options)) {
            $options = call_user_func($options, $this);
        }

        $options = $this->isOptgroup() ? $this->formatOptgroup($options) : $this->toValueLabelArray($options);

        // Check if placeholder is set and add it to the beginning of the options array
        if (isset($this->field['placeholder'])) {
            $placeholder = $this->field['placeholder'];
            $label = $this->getLabel() ? $this->getLabel()->getText() : $this->toHumanReadable($this->getKey());
            $placeholder = [['value' => '', 'label' => trans($placeholder, ['attribute' => $label])]];
            $options = array_merge($placeholder, $options);
        }

        $this->options = $options;
    }

    /**
     * Check format is optgroup
     * 
     * @return bool
     */
    public function isOptgroup(): bool
    {
        return $this->field['optgroup'] ?? false;
    }

    /**
     * Convert options to optgroup
     *
     * @param array $options
     * @return array
     */
    protected function formatOptgroup(array $options): array
    {
        $newOptions = [];
        foreach ($options as $key => $option) {
            if (isset($option['optgroup'])) {
                $optgroup = $option;
                $optgroup['label'] = $optgroup['optgroup'];
                $optgroup['options'] = $this->toValueLabelArray($optgroup['options']);
                $optgroup['optgroup'] = true;
                $newOptions[] = $optgroup;
            } else if (is_array($option)) {
                $newOptions[] = $this->toValueLabelArray([$option])[0];
            } else {
                $newOptions[] = $this->toValueLabelArray([$key => $option])[0];
            }
        }
        return $newOptions;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions(): array
    {
        $options = [];
        foreach ($this->options as $option) {
            if (isset($option['optgroup'])) {
                $gOptions = [];
                foreach ($option['options'] as $gOption) {
                    $attr = [];
                    if ($this->isSelected($gOption['value'])) {
                        $attr['selected'] = true;
                    }
                    $gOptions[] = [...$gOption, ...$attr];
                }
                $options[] = [...$option, 'options' => $gOptions];
            } else {
                $attr = [];
                if ($this->isSelected($option['value'])) {
                    $attr['selected'] = true;
                }
                $options[] = [...$option, ...$attr];
            }
        }
        return $options;
    }

    /**
     * Check if element is multiselect
     *
     * @return bool
     */
    public function isMultiselect(): bool
    {
        return $this->field['multiselect'] ?? false;
    }

    /**
     * Get the element name
     * 
     * @return string
     */
    public function getName(): string
    {
        $name = ($this->isMultiselect()) ? $this->getNameKey() . '[]' : $this->getNameKey();

        return $this->toBracketNotation($name);
    }

    /**
     * Get element`s attributes
     * 
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = parent::getAttributes();

        // Remove 'placeholder' attribute
        unset($attributes['placeholder']);

        // Check is multiselect and add 'multiple' attribute
        if ($this->isMultiselect()) {
            $attributes[] = 'multiple';
        }

        return $attributes;
    }

    public function isSelected($value): bool
    {
        return (
            ($value !== null && $this->getValue() !== null)  &&
            (
                ($this->isMultiselect() && in_array($value, (array) $this->getValue())) ||
                (!$this->isMultiselect() && $value == $this->getValue())
            )
        );
    }

    /**
     * Transforms element to array
     * 
     * @param string $side frontend|backend
     * @return array
     */
    public function toArray($side = 'frontend'): array
    {
        $arr = parent::toArray($side);

        $arr['multiselect'] = $this->isMultiselect();
        $arr['options'] = $this->getOptions();
        return $arr;
    }
}
