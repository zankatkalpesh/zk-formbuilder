<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Element as ElementContract;
use Zk\FormBuilder\Contracts\Form;
use Illuminate\Support\Arr;

class MultipleElement extends Element
{
    /**
     * Blade view component path.
     *
     * @var string
     */
    public $component = 'formbuilder::template.multiple';

    /**
     * Blade view component path.
     *
     * @var string
     */
    public $actionComponent = 'formbuilder::template.multiple.action';

    /**
     * Element type identifier.
     *
     * @var string
     */
    public $elementType = 'multiple';

    /**
     * JavaScript handler/component name.
     *
     * @var string
     */
    public $jsElement = 'ZkMultipleElement';

    /**
     * Javascript action handler/component name.
     * 
     * @var string
     */
    public $jsActionElement = 'ZkMultipleActionElement';

    /**
     * Element orginal fields
     *
     * @var array
     */
    protected $orgFields = [];

    /**
     * Element rows
     *
     * @var array
     */
    protected $rows = [];

    /** 
     * Element row object
     * 
     * @var mixed
     */
    protected $rowObject;

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

        // Min and max messages
        $this->field['minMsg'] = $this->field['minMsg'] ?? ($this->getMin() > 1 ? 'Minimum {min} rows are required' : 'At least one row is required.');
        $this->field['maxMsg'] = $this->field['maxMsg'] ?? 'Maximum {max} rows are allowed';

        // Set replace data
        $this->setReplaceData([
            '{prefix}' => $this->getPrefix(),
            '{minRow}' => $this->getMin(),
            '{maxRow}' => $this->getMax(),
            '{minMsg}' => $this->field['minMsg'],
            '{maxMsg}' => $this->field['maxMsg'],
        ]);

        // Set rows
        $this->setRows();
    }

    /**
     * Set rows
     *
     * @return void
     */
    protected function setRows(): void
    {
        $fields = $this->field['fields'] ?? [];

        if (is_callable($fields)) {
            $fields = call_user_func($fields, $this);
        }

        $this->orgFields = $fields;

        // Make min fields
        for ($i = 0; $i < $this->getMin(); $i++) {
            $this->addRow();
        }
    }

    /**
     * Add new row based on data length 
     * 
     * @param array $data
     * @return void
     */
    public function adjustRowsBasedOnData($data)
    {
        if (empty($data)) {
            return;
        }

        $addCount = 0;
        if ($this->getMax() && count($data) > $this->getMax()) {
            $data = array_slice($data, 0, $this->getMax());
            $addCount = count($data) - count($this->rows);
        } else {
            $addCount = count($data) - count($this->rows);
        }

        if ($addCount > 0) {
            for ($i = 0; $i < $addCount; $i++) {
                $this->addRow();
            }
        }
    }

    /**
     * Get min
     * 
     * @return int
     */
    public function getMin()
    {
        return (int) ($this->field['min'] ?? 1);
    }

    /**
     * Get max
     * 
     * @return int
     */
    public function getMax()
    {
        return (int) ($this->field['max'] ?? 0);
    }

    /**
     * Get orginal fields
     * 
     * @return array
     */
    public function getOrgFields()
    {
        return $this->orgFields;
    }

    /**
     * Get element action component name
     * 
     * @return string
     */
    public function getActionComponent()
    {
        return $this->getConfig('action.component') ?? $this->actionComponent;
    }

    /**
     * Get javascript action element
     * 
     * @return string
     */
    public function getJsActionElement()
    {
        return $this->getConfig('action.jsElement') ?? $this->jsActionElement;
    }

    /** 
     * Add new row
     * 
     * @return void
     */
    public function addRow()
    {
        if ($this->getMax() && count($this->rows) >= $this->getMax()) {
            return;
        }
        $rowIndex = 0;
        if (count($this->rows)) {
            $rowIndex = count($this->rows);
        }

        $this->rows[$rowIndex] = $this->makeRowFields($rowIndex);
    }

    /**
     * Make row form elements
     *
     * @param int $rowIndex
     * @return Zk\FormBuilder\Contracts\Element[]
     */
    public function makeRowFields($rowIndex): array
    {
        $newName = $this->name . '.' . $rowIndex;

        // Define row
        $row = [
            'name' => $newName,
            'fields' => [],
            'rowPrefix' => $this->toBracketNotation($newName)
        ];

        // Make row content
        foreach ($this->orgFields as $name => $field) {
            if (is_numeric($name)) {
                $name = $field['name'];
            }
            $name = $newName . '.' . $name;
            // Make element
            $element = $this->form->makeElement($name, $field, $this);
            if ($element === null) continue;

            $row['fields'][] = $element;
        }

        // Make wrapper
        $row['wrapper'] = $this->getRowWrapper($row);
        // Remove Button
        $row['removeAction'] = $this->getRowRemoveAction($row);

        return $row;
    }

    /**
     * Get Row wrapper
     *
     * @param array $tab
     * @param string $key
     * @return array
     */
    public function getRowWrapper($row, $key = 'rowWrapper'): array
    {
        // Replace the data
        $replaceData = $this->getReplaceData();
        $replaceData['{rowKey}'] = $row['name'];
        $replaceData['{rowPrefix}'] = $row['rowPrefix'];

        // Row Wrapper Builder
        $wBuilder = clone $this->wrapperBuilder;
        $wBuilder->replace($replaceData);
        $wrapper = $this->getConfig($key) ?? [];
        // Add Error class to element
        $errorClass = $wrapper['errorClass'] ?? '';
        $errorClass = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
        $wBuilder->replace('{errorClass}', '');
        if (isset($row['invalid']) && $row['invalid']) {
            $wBuilder->replace('{errorClass}', $errorClass);
        }
        $rowWrapper = $wBuilder->set($wrapper)->build();

        // Field Wrapper Builder
        $fwBuilder = clone $this->wrapperBuilder;
        $fwBuilder->replace($replaceData);
        $fwrapper = $this->getConfig('fieldWrapper') ?? [];
        // Add Error class to element
        $fErrorClass = $fwrapper['errorClass'] ?? '';
        $fErrorClass = str_replace(array_keys($replaceData), array_values($replaceData), $fErrorClass);
        $fwBuilder->replace('{errorClass}', '');
        if (isset($row['invalid']) && $row['invalid']) {
            $fwBuilder->replace('{errorClass}', $fErrorClass);
        }
        $fieldWrapper = $fwBuilder->set($fwrapper)->build();

        return [
            'wrapper' => $rowWrapper,
            'errorClass' => $errorClass,
            'fieldWrapper' => [
                'wrapper' => $fieldWrapper,
                'errorClass' => $fErrorClass
            ]
        ];
    }

    public function getRowRemoveAction($row)
    {
        $action = $this->fetchAction('remove');

        $replaceData = $this->getReplaceData();
        $replaceData['{rowKey}'] = $row['name'];
        $replaceData['{rowPrefix}'] = $row['rowPrefix'];

        $defaltAttr = [
            'data-action' => 'remove-row',
            'data-row-key' => '{rowKey}',
            'data-container-prefix' => '{prefix}',
            'data-remove-prefix' => '{rowPrefix}',
            'data-min-row' => '{minRow}',
            'data-min-msg' => '{minMsg}'
        ];
        $action['attributes'] = $this->replacePattern(array_merge($defaltAttr, $action['attributes']), $replaceData);

        $wBuilder = clone $this->wrapperBuilder;
        $action['wrapper'] = $wBuilder
            ->replace($replaceData)
            ->set($action['wrapper'])
            ->build();

        return [
            'jsElement' => $this->getJsActionElement(),
            'component' => $this->getActionComponent(),
        ] + $action;
    }

    /**
     * Get row prefix
     * 
     * @return string
     */
    public function getPrefix()
    {
        return $this->toBracketNotation($this->name);
    }

    /**
     * Get rows
     * 
     * @return Zk\FormBuilder\Contracts\Element[]
     */
    public function getRows()
    {
        // Set invalid row and Make wrapper
        foreach ($this->rows as $key => $row) {
            // Set invalid row
            $row['invalid'] = $this->isInvalidRow($row);

            $this->rows[$key] = $row;
        }

        return $this->rows;
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

        if ($modifyData !== null && Arr::has($modifyData, $this->getNameKey())) {
            $elmData = Arr::get($modifyData, $this->getNameKey(), []);
            // Format data to save as per column type
            if ($this->getColumnType() === 'json' && !empty($elmData)) {
                $elmData = is_string($elmData) ? json_decode($elmData, true) : $elmData;
            }
            $elmData = array_values($elmData);

            Arr::set($modifyData, $this->getNameKey(), $elmData);
            // Add new row based on data length
            $this->adjustRowsBasedOnData($elmData);
        }

        foreach ($this->rows as $row) {
            foreach ($row['fields'] as $field) {
                $modifyData = $field->modifyData($modifyData);
            }
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

        if ($this->data !== null && Arr::has($this->data, $this->getNameKey())) {
            // Add new row based on data length
            $data = Arr::get($this->data, $this->getNameKey(), []);
            $this->adjustRowsBasedOnData($data);
        }

        foreach ($this->rows as $row) {
            foreach ($row['fields'] as $field) {
                $field->setData($this->data);
            }
        }
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

        foreach ($this->rows as $row) {
            foreach ($row['fields'] as $field) {
                if ($field->hasRules($side)) {
                    return true;
                }
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
        foreach ($this->rows as $row) {
            foreach ($row['fields'] as $field) {
                $field->validate($messages);
            }
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

        foreach ($this->rows as $row) {
            if ($this->isInvalidRow($row)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check is invalid row
     *
     * @return bool
     */
    public function isInvalidRow($row)
    {
        if (!$this->shouldValidate()) {
            return false;
        }

        foreach ($row['fields'] as $field) {
            if ($field->isInvalid()) {
                return true;
            }
        }

        return false;
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
     * Get rows to array
     *
     * @param string $side frontend|backend 
     * @return array
     */
    protected function getRowsToArray($side = 'frontend'): array
    {
        $rows = [];
        foreach ($this->getRows() as $key => $row) {
            foreach ($row['fields'] as $fKey => $field) {
                $row['fields'][$fKey] = $field->toArray($side);
            }
            $rows[$key] = $row;
        }

        return $rows;
    }

    /**
     * Get new row object
     * 
     * @param string $side frontend|backend 
     * @return array
     */
    public function getNewRowObject($side = 'frontend')
    {
        if ($this->rowObject) return $this->rowObject;

        $row = $this->makeRowFields('{{index}}');
        // Set invalid row
        $row['invalid'] = false;
        // Make wrapper
        $row['wrapper'] = $this->getRowWrapper($row);
        foreach ($row['fields'] as $fKey => $field) {
            $row['fields'][$fKey] = $field->toArray($side);
        }
        $row['removeAction'] = $this->getRowRemoveAction($row);
        $row['jsElement'] = $this->getJsElement();
        $this->rowObject = $row;

        return $this->rowObject;
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

        // Add child data to parent
        $childData = Arr::get($newAttributes, $this->getColumnName(), []);

        // Format data to save as per column type
        if ($this->getColumnType() === 'json' && !empty($childData)) {
            $childData = is_string($childData) ? json_decode($childData, true) : $childData;
        }

        // Add new row based on data length
        $this->adjustRowsBasedOnData($childData);

        // Remove child data from parent new attributes
        Arr::forget($newAttributes, $this->getColumnName());

        // Set child data to parent
        Arr::set($newAttributes, $this->getNameKey(), $childData);

        // Set entity attributes
        $entity->setRawAttributes($newAttributes);

        // Load rows
        $childData = [];
        foreach ($this->rows as $row) {
            foreach ($row['fields'] as $field) {
                $childData = array_merge($childData, $field->load($entity));
            }
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
        // Fill rows
        foreach ($this->rows as $row) {
            foreach ($row['fields'] as $field) {
                $field->fill($entity, $data, $emptyOnNull);
            }
        }

        // Get entity attributes and convert to array format 
        $attributes = $entity->getAttributes();
        $newAttributes = Arr::undot($attributes);

        // Add child data to parent
        $childData = Arr::get($newAttributes, $this->getNameKey(), []);

        // Format data to save as per column type
        if ($this->getColumnType() === 'json' && !empty($childData)) {
            $childData = json_encode($childData);
        }
        // Remove child data from parent new attributes
        Arr::forget($newAttributes, $this->getNameKey());

        // Set child data to parent
        Arr::set($newAttributes, $this->getColumnName(), $childData);

        // Set entity attributes
        $entity->setRawAttributes($newAttributes);
    }

    private function fetchAction($name = 'add')
    {
        $action = $this->getConfig('action.' . $name) ?? [];
        $defultKeys = [
            'show' => true,
            'tag' => 'button',
            'attributes' => [],
            'text' => 'Add More',
            'position' => 'before',
            'wrapper' => [],
            'before' => '',
            'after' => '',
        ];
        foreach ($defultKeys as $key => $value) {
            $action[$key] = $action[$key] ?? ($this->getConfig('action.' . $name . '.' . $key) ?? $value);
        }
        $action['before'] = $action['before'] ?? ($this->getConfig('before') ?? '');
        if (is_callable($action['before'])) {
            $action['before'] = call_user_func($action['before'], $this);
        }
        $action['after'] = $action['after'] ?? ($this->getConfig('after') ?? '');
        if (is_callable($action['after'])) {
            $action['after'] = call_user_func($action['after'], $this);
        }

        return $action;
    }

    public function getAddAction()
    {
        $action = $this->fetchAction('add');

        $replaceData = $this->getReplaceData();

        $defaltAttr = [
            'data-container-prefix' => '{prefix}',
            'data-action' => 'add-row',
            'data-max-row' => '{maxRow}',
            'data-max-msg' => '{maxMsg}',
        ];
        $action['attributes'] = $this->replacePattern(array_merge($defaltAttr, $action['attributes']), $replaceData);

        $wBuilder = clone $this->wrapperBuilder;
        $action['wrapper'] = $wBuilder
            ->replace($replaceData)
            ->set($action['wrapper'])
            ->build();

        return [
            'jsElement' => $this->getJsActionElement(),
            'component' => $this->getActionComponent(),
            'rowObject' => $this->getNewRowObject('frontend'),
        ] + $action;
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
            'prefix' => $this->getPrefix(),
            'min' => $this->getMin(),
            'minMsg' => $this->field['minMsg'],
            'max' => $this->getMax(),
            'maxMsg' => $this->field['maxMsg'],
            'type' => $this->getType(),
            'name' => $this->getName(),
            'id' => $this->getId(),
            'nameKey' => $this->getNameKey(),
            'key' => $this->getKey(),
            'label' => $this->getLabel() ? $this->getLabel()->toArray() : false,
            'before' => $this->getBefore(),
            'after' => $this->getAfter(),
            'wrapper' => $this->getWrapper(),
            'contentWrapper' => $this->getWrapper('contentWrapper'),
            'attributes' => $this->getAttributes(),
            // 'value' => $this->getValue(),
            'rowObject' => $this->getNewRowObject($side),
            'rows' => $this->getRowsToArray($side),
            'invalid' => $this->isInvalid(),
            'component' => $this->getComponent(),
            'jsElement' => $this->getJsElement(),
            'elementType' => $this->getElementType(),
            'addAction' => $this->getAddAction(),
            'viewOnly' => $this->hasViewOnly(),
            'properties' => $this->getProperties(),
        ];
    }
}
