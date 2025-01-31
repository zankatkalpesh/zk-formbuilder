<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Illuminate\Support\Arr;
use Zk\FormBuilder\Helpers\WrapperBuilder;

class GroupElement extends Element
{
    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::template.group';

    /**
     * Element type
     *
     * @var string
     */
    public $elementType = 'group';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkGroupElement';

    /**
     * Element fields
     *
     * @var array
     */
    protected $fields = [];

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

        $this->setFields();
    }

    /**
     * Set fields
     *
     * @return void
     */
    protected function setFields(): void
    {
        $fields = $this->field['fields'] ?? [];

        if (is_callable($fields)) {
            $fields = call_user_func($fields, $this);
        }

        $this->fields = $this->makeFields($fields);
    }

    /**
     * Make form elements
     *
     * @return Zk\FormBuilder\Contracts\Element[]
     */
    public function makeFields($fields): array
    {
        $groupName = $this->name;
        // Remove last element name from group name if not parent and has dot in name
        if (!$this->isParent() && strpos($groupName, ".") !== false) {
            $nameArr = explode('.', $groupName);
            $groupName = implode(".", array_slice($nameArr, 0, -1));
        }
        // Remove group name if not parent 
        else if (!$this->isParent()) {
            $groupName = '';
        }

        $elements = [];
        foreach ($fields as $name => $field) {

            if (is_numeric($name)) {
                $name = $field['name'];
            }

            $name = ($groupName ?  $groupName . '.' : '') . $name;
            $element = $this->elementFactory->make(
                $field,
                $name,
                $this,
                $this->getProperties(),
                $this->configPath
            );

            $elements[] = $element;
        }

        return $elements;
    }

    /**
     * Modify element data before element set data
     * 
     * @param mixed $data
     * @return mixed 
     */
    public function modifyData($data): mixed
    {
        $modifyData = $data;

        foreach ($this->getFields() as $field) {
            $modifyData = $field->modifyData($modifyData);
        }

        return $modifyData;
    }

    /**
     * Set element data
     * 
     * @return void
     */
    public function setData($data): void
    {
        $this->data = $data;

        foreach ($this->getFields() as $field) {
            $field->setData($this->data);
        }
    }

    /**
     * Get field of group
     * 
     * @return Zk\FormBuilder\Contracts\Element[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Determine if element has rules
     *
     * @param string $side
     * @return bool
     */
    public function hasRules(string $side = 'backend'): bool
    {
        foreach ($this->getFields() as $field) {
            if ($field->hasRules($side)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate element
     *
     * @param string $prefix
     * @return void
     */
    public function validate(array $messages = [])
    {
        if (!$this->shouldValidate()) {
            return;
        }

        // Validate fields
        foreach ($this->getFields() as $field) {
            $field->validate($messages);
        }
    }

    /**
     * Check is invalid
     *
     * @return bool
     */
    public function isInvalid(): bool
    {
        if ($this->validator === null) {
            return false;
        }

        foreach ($this->getFields() as $field) {
            if ($field->isInvalid()) {
                return true;
            }
        }

        return false;
    }

    /** 
     * Check is parent
     * 
     * @return bool
     */
    public function isParent(): bool
    {
        return $this->field['parent'] ?? false;
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
     * Get field of form to array
     *
     * @param string $side frontend|backend
     * @return array
     */
    protected function getFieldsToArray($side = 'frontend'): array
    {
        $fields = [];
        foreach ($this->getFields() as $key => $field) {
            $fields[$key] = $field->toArray($side);
        }

        return $fields;
    }

    /**
     * Load element data from entity
     *
     * @param Illuminate\Database\Eloquent\Model $entity
     * @return array
     */
    public function load($entity): array
    {
        if ($entity === null || !$this->isPersist()) {
            return [];
        }

        // Get entity attributes and convert to array format 
        $attributes = $entity->getAttributes();
        $newAttributes = Arr::undot($attributes);

        // If element is parent, add child data to parent 
        if ($this->isParent()) {
            // Add child data to parent
            $childData = Arr::get($newAttributes, $this->getColumnName(), []);
            Arr::forget($newAttributes, $this->getColumnName());
            // Format data to save as per column type
            if ($this->getColumnType() === 'json' && !empty($childData)) {
                $childData = json_decode($childData, true);
            }
            // Set child data to parent
            Arr::set($newAttributes, $this->getNameKey(), $childData);
        }

        // Set entity attributes
        $entity->setRawAttributes($newAttributes);

        // Load fields
        $childData = [];
        foreach ($this->getFields() as $field) {
            // $childData = Arr::prepend($childData, Arr::undot($field->load($entity)));
            $childData = array_merge($childData, $field->load($entity));
        }

        return $childData;
    }

    /**
     * Fill element data to entity
     *
     * @param Illuminate\Database\Eloquent\Model $entity
     * @param array $data
     * @param bool $emptyOnNull
     * @return void
     */
    public function fill($entity, $data, $emptyOnNull = true)
    {
        if (!$this->isPersist()) {
            return;
        }

        // Fill fields
        foreach ($this->getFields() as $field) {
            $field->fill($entity, $data, $emptyOnNull);
        }

        // Get entity attributes and convert to array format 
        $attributes = $entity->getAttributes();
        $newAttributes = Arr::undot($attributes);

        // If element is parent, add child data to parent 
        if ($this->isParent()) {
            // Add child data to parent
            $childData = Arr::get($newAttributes, $this->getNameKey(), []);
            Arr::forget($newAttributes, $this->getNameKey());
            // Format data to save as per column type
            if ($this->getColumnType() === 'json' && !empty($childData)) {
                $childData = json_encode($childData);
            }
            // Set child data to parent
            Arr::set($newAttributes, $this->getColumnName(), $childData);
        }

        // Set entity attributes
        $entity->setRawAttributes($newAttributes);
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
            'parent' => $this->isParent(),
            'type' => $this->getType(),
            'name' => $this->getName(),
            'id' => $this->getId(),
            'nameKey' => $this->getNameKey(),
            'key' => $this->getKey(),
            'label' => $this->getLabel() ? $this->getLabel()->toArray() : false,
            'before' => $this->getBefore(),
            'after' => $this->getAfter(),
            'wrapper' => $this->getWrapper(),
            'fieldWrapper' => $this->getWrapper('fieldWrapper'),
            'attributes' => $this->getAttributes(),
            // 'value' => $this->getValue(),
            'fields' => $this->getFieldsToArray($side),
            'invalid' => $this->isInvalid(),
            'component' => $this->getComponent(),
            'jsElement' => $this->getJsElement(),
            'elementType' => $this->getElementType(),
            'viewOnly' => $this->hasViewOnly(),
        ];
    }
}
