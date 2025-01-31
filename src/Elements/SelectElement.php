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
            $placeholder = [['value' => '', 'label' => trans($placeholder)]];
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
        return $this->options;
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
        $name = $this->toBracketNotation($this->name);

        return ($this->isMultiselect()) ? $name . '[]' : $name;
    }

    /**
     * Get the element id
     * 
     * @return string
     */
    public function getId(): string
    {
        $name = $this->toBracketNotation($this->name);

        $id = $this->field['id'] ?? $this->getConfigByKey('id', 'input');

        if (empty($id)) {
            $id = $name;
        }

        $id = $this->toBracketNotation($id);

        return str_replace('{name}', $name, $id);
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

    /**
     * Print option attributes
     * 
     * @param array $attributes
     * @param array $exclude // The attribute to exclude from the output.
     * @return string
     */
    public function optionAttributes(array $attributes = [], $exclude = []): string
    {
        $optAttributes = [];
        foreach ($attributes as $key => $value) {
            if (in_array($key, $exclude)) continue;
            $optAttributes[] = (is_numeric($key)) ? trim((string) $value) : $key . '="' . trim((string) $value) . '"';
        }
        // Check if a 'value' attribute exists and set 'selected' if applicable
        $value = $attributes['value'] ?? null;
        $selected = $this->isSelected($value);
        if ($selected) {
            $optAttributes[] = 'selected';
        }

        return implode(' ', $optAttributes);
    }

    public function isSelected($value): bool
    {
        return (
            ($value && $this->getData() !== null)  &&
            (
                ($this->isMultiselect() && in_array($value, (array) $this->getData())) ||
                (!$this->isMultiselect() && $value == $this->getData())
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
