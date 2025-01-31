<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

class RadioElement extends Element
{
    /**
     * Element type
     *
     * @var string
     */
    public $elementType = 'radio';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkRadioElement';

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

        // Check if element is parent of RadiogroupElement and `checked` is true
        if (
            $this->parent &&
            $this->parent instanceof RadiogroupElement &&
            $this->parent->getData() !== null
        ) {
            if ($this->getValue() == $this->parent->getData()) {
                $attributes['checked'] = 'checked';
            }
        }

        return $attributes;
    }
}
