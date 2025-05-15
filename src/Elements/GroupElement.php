<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Element as ElementContract;
use Zk\FormBuilder\Contracts\Form;
use Illuminate\Support\Arr;

class GroupElement extends Element
{
    /**
     * Blade view component path.
     *
     * @var string
     */
    public $component = 'formbuilder::template.group';

    /**
     * Element type identifier.
     *
     * @var string
     */
    public $elementType = 'group';

    /**
     * JavaScript handler/component name.
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

        // Make group fields
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
            // Make element
            $element = $this->form->makeElement($name, $field, $this);
            if ($element === null) continue;

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

        if ($this->modifyData && is_callable($this->modifyData)) {
            $modifyData = call_user_func($this->modifyData, $modifyData, $this);
            return $modifyData;
        }

        // If element is parent, add child data to parent 
        if ($this->isParent() && ($modifyData !== null && Arr::has($modifyData, $this->getNameKey()))) {
            $elmData = Arr::get($modifyData, $this->getNameKey(), []);
            // Format data to save as per column type
            if ($this->getColumnType() === 'json' && !empty($elmData)) {
                $elmData = is_string($elmData) ? json_decode($elmData, true) : $elmData;
            }
            Arr::set($modifyData, $this->getNameKey(), $elmData);
        }

        foreach ($this->fields as $field) {
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

        foreach ($this->fields as $field) {
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
        $this->initRules();

        foreach ($this->fields as $field) {
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
        foreach ($this->fields as $field) {
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
        if (!$this->shouldValidate()) {
            return false;
        }

        foreach ($this->fields as $field) {
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
                $childData = is_string($childData) ? json_decode($childData, true) : $childData;
            }
            // Set child data to parent
            Arr::set($newAttributes, $this->getNameKey(), $childData);
        }

        // Set entity attributes
        $entity->setRawAttributes($newAttributes);

        // Load fields
        $childData = [];
        foreach ($this->fields as $field) {
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
        if (!$this->isPersist() || $this->hasViewOnly()) {
            return;
        }

        // Fill fields
        foreach ($this->fields as $field) {
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
            'properties' => $this->getProperties(),
        ];
    }
}
