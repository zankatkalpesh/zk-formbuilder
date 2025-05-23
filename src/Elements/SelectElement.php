<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Element as ElementContract;
use Zk\FormBuilder\Contracts\Form;
use Illuminate\Support\Arr;

class SelectElement extends Element
{
    /**
     * Blade view component path.
     *
     * @var string
     */
    public $component = 'formbuilder::template.select';

    /**
     * Element type identifier.
     *
     * @var string
     */
    public $elementType = 'select';

    /**
     * JavaScript handler/component name.
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
     * Element constructor.
     *
     * Initializes the base state of the element, including its field data,
     * parent reference, configuration path, and injected dependencies.
     * Heavy logic like dynamic field setup should be handled in `init()`,
     * which must be called explicitly after successful instantiation.
     *
     * @param array $field Raw field definition, including name, type, label, etc.
     * @param Form $form
     * @param ElementContract | null $parent The parent element or null containing this element.
     * @param WrapperBuilder $wrapperBuilder Helper for rendering element wrappers.
     */
    public function __construct(
        array $field,
        protected Form $form,
        protected ElementContract | null $parent,
        protected WrapperBuilder $wrapperBuilder
    ) {
        parent::__construct($field, $form, $parent, $wrapperBuilder);
    }

    /**
     * Initialize the element's dynamic properties.
     *
     * This method should be called after the element is successfully constructed,
     * allowing for conditional rendering and dependency resolution before setup.
     *
     * @return void
     */
    public function init(): void
    {
        parent::init();

        // Set options
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
     * Get element data
     * 
     * @return mixed
     */
    public function getData()
    {
        if ($this->data === null || !Arr::has($this->data, $this->getNameKey())) {
            return null;
        }

        $data = (array) Arr::get($this->data, $this->getNameKey(), []);

        if (empty($this->options)) {
            return $this->isMultiselect() ? $data : reset($data);
        }

        // Match values with options
        $validOptions = [];
        foreach ($this->options as $option) {
            if (!empty($option['optgroup'])) {
                foreach ($option['options'] as $gOption) {
                    if (empty($gOption['disabled'])) {
                        $validOptions[$gOption['value']] = true;
                    }
                }
            } elseif (empty($option['disabled'])) {
                $validOptions[$option['value']] = true;
            }
        }
        $validValues = [];
        foreach ($data as $value) {
            if (isset($validOptions[$value])) {
                $validValues[] = $value;
            }
        }

        return empty($validValues) ? null : ($this->isMultiselect() ? $validValues : reset($validValues));
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return array_map(function ($option) {
            if (!empty($option['optgroup'])) {
                $option['options'] = array_map(function ($gOption) {
                    if ($this->isSelected($gOption['value'])) {
                        $gOption['selected'] = true;
                    }
                    return $gOption;
                }, $option['options']);
            } elseif ($this->isSelected($option['value'])) {
                $option['selected'] = true;
            }
            return $option;
        }, $this->options);
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
            $attributes['multiple'] = 'true';
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
