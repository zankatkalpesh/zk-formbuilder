<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Element as ElementContract;
use Zk\FormBuilder\Contracts\Form;
use Illuminate\Support\Arr;

class RadiogroupElement extends Element
{
    /**
     * Blade view component path.
     *
     * @var string
     */
    public $component = 'formbuilder::template.itemgroup';

    /**
     * Element type identifier.
     *
     * @var string
     */
    public $elementType = 'radiogroup';

    /**
     * JavaScript handler/component name.
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

        // Set items
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

        $this->items = $this->toMakeItemsAsElement($items, 'radio');
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
     * Get element data
     * 
     * @return mixed
     */
    public function getData()
    {
        if ($this->data === null || !Arr::has($this->data, $this->getNameKey())) {
            return null;
        }

        $data = Arr::get($this->data, $this->getNameKey(), null);

        if ($data === null || !collect($this->getItems())->contains(fn($item) => $item->getValue() == $data)) {
            return null;
        }

        return $data;
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
            'rules' => $this->getRules($side),
            'invalid' => $this->isInvalid(),
            'error' => $this->getError() ? $this->getError()->toArray() : false,
            'component' => $this->getComponent(),
            'jsElement' => $this->getJsElement(),
            'elementType' => $this->getElementType(),
            'viewOnly' => $this->hasViewOnly(),
            'properties' => $this->getProperties(),
            'isRequired' => $this->isRequired(),
        ];
    }
}
