<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

class CheckboxElement extends Element
{
    /**
     * JavaScript handler/component name.
     *
     * @var string
     */
    public $jsElement = 'ZkCheckboxElement';

    /**
     * Element type identifier.
     *
     * @var string
     */
    public $elementType = 'checkbox';

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

        // Check is element `checked` is true
        if ($this->getData() !== null) {
            $attributes['checked'] = 'checked';
        }

        // Check if element is parent of CheckboxgroupElement and `checked` is true
        if (
            $this->parent &&
            $this->parent instanceof CheckboxgroupElement &&
            $this->parent->getData() !== null
        ) {
            if (is_array($this->parent->getData())) {
                if (in_array($this->getValue(), $this->parent->getData())) {
                    $attributes['checked'] = 'checked';
                }
            } else {
                if ($this->getValue() == $this->parent->getData()) {
                    $attributes['checked'] = 'checked';
                }
            }
        }

        return $attributes;
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

        if (
            $this->parent &&
            $this->parent instanceof CheckboxgroupElement
        ) {
            $arr['rules'] = $this->parent->getRules($side);
        }

        return $arr;
    }
}
