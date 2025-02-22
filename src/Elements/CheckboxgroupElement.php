<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Zk\FormBuilder\Helpers\WrapperBuilder;

class CheckboxgroupElement extends Element
{
    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::template.itemgroup';

    /**
     * Element type
     *
     * @var string
     */
    public $elementType = 'checkboxgroup';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkItemgroupElement';

    /**
     * Element items
     *
     * @var array
     */
    protected $items = [];

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

        $this->setItems();
    }

    /**
     * Set items
     *
     * @return void
     */
    protected function setItems(): void
    {
        $items = $this->field['items'] ?? [];

        if (is_callable($items)) {
            $items = call_user_func($items, $this);
        }

        $items = $this->toValueLabelArray($items);

        $this->items = $this->toMakeItemsAsElement($items, 'checkbox');
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Set element data
     * 
     * @return void
     */
    public function setData($data): void
    {
        $this->data = $data;
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

        return $attributes;
    }

    /**
     * Get item to array
     * 
     * @param string $side frontend|backend
     * @return array
     */
    protected function getItemsToArray($side = 'frontend'): array
    {
        $items = [];
        foreach ($this->getItems() as $key => $item) {
            $items[$key] = $item->toArray($side);
        }

        return $items;
    }

    /**
     * Transforms element to array
     * 
     * @param string $side frontend|backend
     * @return array
     */
    public function toArray($side = 'frontend'): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'id' => $this->getId(),
            'nameKey' => $this->getNameKey(),
            'key' => $this->getKey(),
            'label' => $this->getLabel() ? $this->getLabel()->toArray() : false,
            'before' => $this->getBefore(),
            'after' => $this->getAfter(),
            'wrapper' => $this->getWrapper(),
            'itemWrapper' => $this->getWrapper('itemWrapper'),
            'attributes' => $this->getAttributes(),
            'value' => $this->getValue(),
            'items' => $this->getItemsToArray($side),
            'rules' => $this->getRules('frontend'),
            'invalid' => $this->isInvalid(),
            'error' => $this->getError() ? $this->getError()->toArray() : false,
            'component' => $this->getComponent(),
            'jsElement' => $this->getJsElement(),
            'elementType' => $this->getElementType(),
            'viewOnly' => $this->hasViewOnly(),
        ];
    }
}
