<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Illuminate\Support\Arr;
use Zk\FormBuilder\Elements\Factory;
use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Element as ElementContract;
use Zk\FormBuilder\Contracts\Form;
use Zk\FormBuilder\Traits\GeneralMethods;
use Zk\FormBuilder\Contracts\Validation\Validator;
use Zk\FormBuilder\Contracts\Label;
use Zk\FormBuilder\Contracts\Error;

class Element implements ElementContract
{
    use GeneralMethods;

    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::template.input';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkElement';

    /**
     * Element type
     *
     * @var string
     */
    public $elementType = 'general';

    /**
     * Element type
     *
     * @var string
     */
    public $type = 'text';

    /**
     * Element key on data
     *
     * @var string
     */
    public $name;

    /**
     * Element key on entity
     *
     * @var array
     */
    public $attributes;

    /**
     * Element view only
     * 
     * @var bool
     */
    public $viewOnly = false;

    /**
     * Element modify value
     * 
     * @var mixed
     */
    public $modifyValue;

    /** 
     * Element modify data
     * 
     * @var mixed
     */
    public $modifyData;

    /**
     * Whether should persist in db
     *
     * @var bool
     */
    public $persist = true;

    /**
     * Element data
     *
     * @var any
     */
    public $data;

    /**
     * Element field
     *
     * @var array
     */
    public $field;

    /**
     * Element properties
     *
     * @var array
     */
    public $properties = [];

    /**
     * Element config path
     *
     * @var string
     */
    public $configPath;

    /**
     * Validation rules
     *
     * @var array
     */
    public $rules;

    /**
     * Custom validation messages
     *
     * @var array
     */
    public $messages = [];

    /**
     * Element default replace pattern data
     * 
     * @var array
     */
    protected $replaceData = [];

    /** 
     * Element validator
     * 
     * @var Validator
     */
    protected $validator;

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
        $this->field = $field;
        $this->validator = $parent->getValidator();
        $this->properties = array_merge($this->properties, $properties);
        $this->configPath = $configPath;

        $this->init();
    }

    /**
     * Initalize class properties
     * 
     * @return void
     */
    public function init()
    {
        $this->name = $this->field['name'] ?? $this->name;
        $this->type = $this->field['type'] ?? $this->type;
        $this->messages = $this->field['messages'] ?? $this->messages;
        $this->persist = $this->field['persist'] ?? $this->persist;
        $this->attributes = $this->getConfigByKey('attributes') ?? [];
        $this->viewOnly = $this->getConfigByKey('viewOnly') != null ? $this->getConfigByKey('viewOnly') : $this->parent->hasViewOnly();
        $this->modifyValue = $this->getConfigByKey('modifyValue') ?? null;
        $this->modifyData = $this->getConfigByKey('modifyData') ?? null;
    }

    /**
     * Get element component name
     * 
     * @return string
     */
    public function getComponent(): string
    {
        return $this->getConfigByKey('component') ?? $this->component;
    }

    /**
     * Get element type
     * 
     * @return string
     */
    public function getElementType(): string
    {
        return $this->elementType;
    }

    /**
     * Get javascript element
     * 
     * @return string
     */
    public function getJsElement(): string
    {
        return $this->getConfigByKey('jsElement') ?? $this->jsElement;
    }

    /**
     * Get property
     * 
     * @param string $key
     * @return mixed
     */
    public function getProperty(string $key)
    {
        return Arr::get($this->properties, $key);
    }

    /**
     * Get properties
     * 
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Get config
     * 
     * @param string $key optional 
     * @return mixed
     */
    public function getConfig($key = null)
    {
        $config = config($this->configPath);
        if ($key) {
            return Arr::get($config, $key, null);
        }

        return $config;
    }

    /**
     * Get element input before
     * 
     * @return mixed
     */
    public function getBefore()
    {
        $before = $this->getConfigByKey('before') ?? '';

        if (is_callable($before)) {
            $before = call_user_func($before, $this);
        }

        return $before;
    }

    /**
     * Get element input after
     * 
     * @return mixed
     */
    public function getAfter()
    {
        $after = $this->getConfigByKey('after') ?? '';

        if (is_callable($after)) {
            $after = call_user_func($after, $this);
        }

        return $after;
    }

    /**
     * Element has view format
     * 
     * @return bool
     */
    public function hasView(): bool
    {
        return ($this->getConfigByKey('view')) ? true : false;
    }

    /**
     * Get element view format
     * 
     * @return mixed
     */
    public function getView()
    {
        $after = $this->getConfigByKey('view') ?? '';

        if (is_callable($after)) {
            $after = call_user_func($after, $this);
        }

        return $after;
    }

    /**
     * Get element`s attributes
     * 
     * @return array
     */
    public function getAttributes(): array
    {
        $class = $this->getConfigByKey('class', 'input') ?? '';
        $attributes = [
            'class' => $class ?? '',
            'name' => $this->getName(),
            'id' => $this->getId(),
        ];

        $attributes = array_merge($this->attributes, $attributes);

        if ($attributes['placeholder'] ?? false) {
            $label = $this->getLabel() ? $this->getLabel()->getText() : $this->toHumanReadable($this->getKey());
            $attributes['placeholder'] = trans($attributes['placeholder'], ['attribute' => $label]);
        }

        $replaceData = $this->getReplaceData();

        // Add error class to element
        $replaceData['{errorClass}'] = '';
        if ($this->isInvalid()) {
            $errorClass = $this->getConfigByKey('errorClass', 'input') ?? '';
            $replaceData['{errorClass}'] = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
        }
        $attributes = $this->replacePattern($attributes, $replaceData);

        return $attributes;
    }

    /**
     * Has element view only
     * 
     * @return bool
     */
    public function hasViewOnly(): bool
    {
        return $this->viewOnly;
    }

    /**
     * Get element name key
     * 
     * @return string
     */
    public function getNameKey(): string
    {
        $nameKey = $this->name;
        if (str_ends_with($nameKey, '[]')) {
            $nameKey = substr($nameKey, 0, -2);
        }

        return $nameKey;
    }

    /**
     * Get the element name in bracket notation
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->toBracketNotation($this->name);
    }

    /** 
     * Get the element key
     * 
     * @return string
     */
    public function getKey(): string
    {
        $key = $this->getNameKey();

        if (strpos($key, ".") !== false) {
            $keyArr = explode('.', $key);
            $key = end($keyArr);
        }

        return $key;
    }

    /**
     * Get the element table column name
     * 
     * @return string
     */
    public function getColumnName(): string
    {
        $column = $this->field['column'] ?? [];

        if (is_array($column)) {
            $column =  $column['name'] ?? '';
        }

        if (empty($column)) {
            return $this->getNameKey();
        }

        // Replace with column name if column name is set
        $key = $this->getNameKey();

        if (strpos($key, ".") !== false) {
            $keyArr = explode('.', $key);
            $keyArr[count($keyArr) - 1] = $column;
            return implode('.', $keyArr);
        }

        return $column;
    }

    /**
     * Get the element table column type
     * 
     * @return string
     */
    public function getColumnType(): string
    {
        $column = $this->field['column'] ?? '';

        if (is_array($column)) {
            return $column['type'] ?? '';
        }

        return '';
    }

    /**
     * Get the element id
     * 
     * @return string
     */
    public function getId(): string
    {
        $name = $this->getNameKey();

        $id = $this->field['id'] ?? $this->getConfigByKey('id', 'input');

        if (empty($id)) {
            $id = $name;
        }

        $id = $this->toBracketNotation($id);

        return str_replace('{name}', $name, $id);
    }

    /**
     * Get the element type
     * 
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the element label property
     * 
     * @return mixed
     */
    public function getLabelProperty()
    {
        return $this->field['label'] ?? [];
    }

    /**
     * Get the element label
     * 
     * @return Label | bool
     */
    public function getLabel(): Label | bool
    {
        $showLabel = (isset($this->field['label']) && $this->field['label'] === false) ? false : true;

        return ($showLabel)
            ? app()->makeWith(__NAMESPACE__ . '\\' . 'Label', ['element' => $this, 'configPath' => $this->configPath])
            : false;
    }

    /**
     * Modify element data before element set data
     * 
     * @param mixed $data
     * @return mixed 
     */
    public function modifyData($data): mixed
    {
        if ($this->modifyData && is_callable($this->modifyData)) {
            $data = call_user_func($this->modifyData, $data, $this);
        }

        return $data;
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
        return $this->data !== null && Arr::has($this->data, $this->getNameKey())
            ? Arr::get($this->data, $this->getNameKey())
            : null;
    }

    /**
     * Get the element default value
     * 
     * @return any
     */
    public function getValue()
    {
        $value = ($this->getData() !== null) ? $this->getData() : ($this->field['value'] ?? '');

        if ($this->modifyValue && is_callable($this->modifyValue)) {
            $value = call_user_func($this->modifyValue, $value, $this);
        }

        return $value;
    }

    /**
     * Get wrapper
     *
     * @param string $key default 'wrapper'
     * @return array
     */
    public function getWrapper(string $key = 'wrapper'): array
    {
        $wrapper = $this->getConfigByKey($key) ?? [];

        // Replace the data
        $replaceData = $this->getReplaceData();
        // Add Error class to element
        $replaceData['{errorClass}'] = '';
        if ($this->isInvalid()) {
            $errorClass = $this->getConfigByKey($key . '.errorClass') ?? '';
            $replaceData['{errorClass}'] = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
        }

        // Wrapper Builder
        $wBuilder = clone $this->wrapperBuilder;
        return $wBuilder
            ->replace($replaceData)
            ->set($wrapper)
            ->build();
    }

    /**
     * Init element rules
     * 
     * @return void
     */
    public function initRules(): void
    {
        if ($this->rules) return;

        $rules = $this->field['rules'] ?? $this->rules;
        if (is_callable($rules)) {
            $rules = call_user_func($rules, $this);
        }
        $this->rules = $rules;
    }

    /**
     * Element is required
     * 
     * @param string $side
     * @return bool
     */
    public function isRequired(string $side = 'backend'): bool
    {
        if (!$this->hasRules($side)) {
            return false;
        }

        $rules = $this->getRules($side);

        return (is_array($rules) && in_array('required', $rules, true) || (is_string($rules) && str_contains($rules, 'required')));
    }

    /**
     * Return rules for side
     *
     * @param string $side
     * @return mixed
     */
    public function getRules(string $side = 'backend')
    {
        if (!$this->hasRules($side)) {
            return;
        }

        $rules = $this->rules;

        if ($this->rulesHas($side)) {
            $rules = $rules[$side];
        }

        return $rules;
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

        if (is_array($this->rules)) {
            if (array_key_exists($side, $this->rules)) {
                return true;
            }

            if (array_key_exists('frontend', $this->rules) || array_key_exists('backend', $this->rules)) {
                return false;
            }
        }

        return $this->rules !== null;
    }

    /**
     * Get element validation messages
     * 
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Determine if rules has certain key
     *
     * @param string $key
     * @param mixed $rules
     * @return void
     */
    protected function rulesHas($key, $rules = null)
    {
        if ($rules === null) {
            $rules = $this->rules;
        }

        return is_array($rules) && array_key_exists($key, $rules);
    }

    /** 
     * Get parent element
     * 
     * @return Element|null
     */
    public function getParent()
    {
        return $this->parent instanceof Element ? $this->parent : null;
    }

    /** 
     * Get form
     * 
     * @return Form
     */
    public function getForm()
    {
        return $this->parent instanceof Form ? $this->parent : $this->parent->getForm();
    }

    /**
     * Get Validator
     *
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Determine if element should be validated
     *
     * @return bool
     */
    public function shouldValidate(): bool
    {
        return $this->validator && $this->hasRules() && !$this->hasViewOnly();
    }

    /**
     * Convert Element data to validation format
     *
     * @return array
     */
    public function toValidationData(): array
    {
        return [$this->getNameKey() => $this->getData()];
    }

    /**
     * Convert Element rule to validation format
     * 
     * @param string $side
     * @return array
     */
    public function toValidationRules(string $side = 'backend'): array
    {
        return [$this->getNameKey() => $this->getRules($side)];
    }

    /**
     * Convert Element attributes to validation
     * 
     * @return array
     */
    public function toValidationAttributes(): array
    {
        // Set element attributes
        $label = $this->getLabel()
            ? $this->getLabel()->getText()
            : $this->toHumanReadable($this->getKey());

        return [$this->getNameKey() => $label];
    }

    /**
     * Return custom message for rule
     *
     * @param string|object $rule
     * @return void
     */
    public function toValidationMessage($rule)
    {
        $ruleName = $rule;

        if (!is_string($rule)) {
            if ($rule instanceof \Illuminate\Validation\Rules\Unique) {
                $ruleName = 'unique';
            } elseif ($rule instanceof \Illuminate\Validation\Rules\Exists) {
                $ruleName = 'exists';
            }
        }

        if (array_key_exists($ruleName, $this->messages)) {
            return $this->messages[$ruleName];
        }

        return;
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

        // Set element data
        $this->validator->setData($this->toValidationData());

        // Set element rules
        $this->validator->setRules($this->toValidationRules());

        // Set element attributes
        $this->validator->addAttributes($this->toValidationAttributes());

        // Set element to message
        if (!empty($messages)) {
            $this->messages = array_merge($this->messages, $messages);
        }
        $this->validator->addMessages($this, $this->getRules(), $this->getNameKey());
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

        return $this->validator->isInvalid($this->getNameKey());
    }

    /**
     * Get the value of errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        if ($this->isInvalid() == false) {
            return [];
        }

        return $this->validator->getError($this->getNameKey());
    }

    /**
     * Get the element error property
     * 
     * @return any
     */
    public function getErrorProperty()
    {
        return $this->field['error'] ?? [];
    }

    /**
     * Get the element label
     * 
     * @return Error | bool
     */
    public function getError(): Error | bool
    {
        return ($this->isInvalid())
            ? app()->makeWith(__NAMESPACE__ . '\\' . 'Error', ['element' => $this, 'configPath' => $this->configPath])
            : false;
    }

    /**
     * Check if element is presented in db
     *
     * @return bool
     */
    public function isPersist()
    {
        return $this->persist;
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

        $data = Arr::get($entity->toArray(), $this->getColumnName());
        // Format data to save as per column type
        if ($this->getColumnType() === 'json' && !empty($data)) {
            $data = json_decode($data, true);
        }

        return [
            $this->getNameKey() => $data
        ];
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

        if (Arr::has($data, $this->getNameKey())) {
            $elData = Arr::get($data, $this->getNameKey());
            // Format data to save as per column type
            if ($this->getColumnType() === 'json' && !empty($elData)) {
                $elData = json_encode($elData);
            }
            $entity[$this->getColumnName()] = $elData;
        } else if ($emptyOnNull) {
            $this->empty($entity);
        }
    }

    /**
     * Empty element value on entity
     *
     * @param object $entity
     * @return void
     */
    public function empty($entity)
    {
        if (!$this->isPersist()) {
            return;
        }

        $value = $this->field['value'] ?? null;
        $entity[$this->getColumnName()] = $value;
    }

    /**
     * Set replace data
     * 
     * @param array | string $data
     * @param string $value optional
     * @return self
     */
    public function setReplaceData(array | string $data, string $value = ''): void
    {
        if (is_array($data)) {
            $this->replaceData = array_merge($this->replaceData, $data);
        } else {
            $this->replaceData[$data] = $value;
        }
    }

    /**
     * Replace Data
     * 
     * @return array
     */
    protected function getReplaceData()
    {
        // Replace the data
        $replaceData = $this->getConfigByKey('replace') ?? [];
        $replaceData['{name}'] = $this->getName();
        $replaceData['{id}'] = $this->getId();
        $replaceData['{type}'] = $this->getType();
        $replaceData['{label}'] = $this->getLabel() ? $this->getLabel()->getText() : '';

        // Merge replace pattern data
        $replaceData = array_merge($replaceData, $this->replaceData);

        return $replaceData;
    }

    /**
     * Get element config by key
     * 
     * @param string $key The key to search for within the element configuration.
     * @param string $group The configuration group to search within.
     * @return mixed The configuration value associated with the key, or null if not found.
     */
    protected function getConfigByKey($key, $group = null)
    {
        // Try to get the configuration directly from the 'field' array
        $keyConfig = Arr::get($this->field, $key, null);
        if ($keyConfig !== null) {
            return $keyConfig;
        }

        // If not found in 'field', heck type-specific configuration
        $keyConfig = $this->getConfig('type.' . $this->getType() . '.' . $key);
        if ($keyConfig !== null) {
            return $keyConfig;
        }
        // As a last resort, check general 'field.{$group?}.{key}' configuration
        $keyConfig = $this->getConfig('field' . ($group ? '.' . $group : '') . '.' . $key);

        return $keyConfig;
    }

    /**
     * Convert items to associative with keys value, label format
     * 
     * @param array $items
     * @return array The formatted array with 'value' and 'label' keys for each item.
     */
    public function toValueLabelArray(array $items): array
    {

        if (empty($items)) {
            return $items;
        }

        // Check array with keys value, label format
        $fisrtItem = reset($items);
        if (isset($fisrtItem['value']) && isset($fisrtItem['label'])) {
            return $items;
        }

        // Check array is associative
        if (array_keys($items) === range(0, count($items) - 1)) {
            $items = array_combine($items, $items);
        }
        // Convert array is associative with keys value, label format
        $items = array_map(function ($value, $label) {
            if (is_array($label) && !array_diff(['value', 'label'], array_keys($label))) {
                return $label;
            }
            return ['value' => $value, 'label' => $label];
        }, array_keys($items), $items);

        return $items;
    }

    /**
     * Make items as element
     *
     * @param array $items
     * @param string $type
     * @return Element[]
     */
    public function toMakeItemsAsElement(array $items, string $type): array
    {
        $newItems = [];
        $itemConfig = $this->field['itemConfig'] ?? [];
        foreach ($items as $key => $item) {
            $value = $item['value'];
            $label = $item['label'];
            $name = $this->field['name'];
            $id = $this->field['id'] ?? $this->field['name'];
            // Set key for item
            $itemKey = $key;
            if (isset($item['key'])) {
                $itemKey = $item['key'];
                $name = $name . '.' . $itemKey;
                unset($item['key']);
            } else if ($type !== 'radio') {
                $name = $name . '[]';
            }
            $id = $id . '.' . $itemKey;

            // Remove value and label from item
            unset($item['value'], $item['label']);
            // Check if label is false
            if ($label !== false) {
                // Label config merge
                if (!is_array($label)) {
                    $label = ['text' => $label];
                }
                $label = array_merge($itemConfig['label'] ?? [], $label);
            }
            // Field config
            $field = [];
            $field['type'] = $type;
            $field['name'] = $name;
            $field['value'] = $value;
            $field['label'] = $label;
            $field['id'] = $id;
            // Merge item with item config
            $item = array_merge($itemConfig, $item);
            // remove label from item
            if (isset($item['label'])) unset($item['label']);
            // Merge field with item
            $field = array_merge($field, $item);
            // Make field
            $element = $this->elementFactory->make(
                $field,
                $field['name'],
                $this,
                $this->getProperties(),
                $this->configPath
            );
            $newItems[] = $element;
        }

        return $newItems;
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
            'inputWrapper' => $this->getWrapper('inputWrapper'),
            'attributes' => $this->getAttributes(),
            'value' => $this->getValue(),
            'rules' => $this->getRules($side),
            'messages' => (!empty($this->getMessages()) ? $this->getMessages() : ''),
            'invalid' => $this->isInvalid(),
            'error' => $this->getError() ? $this->getError()->toArray() : false,
            'component' => $this->getComponent(),
            'jsElement' => $this->getJsElement(),
            'elementType' => $this->getElementType(),
            'viewOnly' => $this->hasViewOnly(),
            'view' => $this->hasView() ? $this->getView() : false,
            'properties' => $this->getProperties(),
        ];
    }
}
