<?php

namespace Zk\FormBuilder\Contracts;

use Zk\FormBuilder\Contracts\Validation\Validator;
use Zk\FormBuilder\Contracts\Label;
use Zk\FormBuilder\Contracts\Error;

interface Element
{
    /**
     * Get Component
     * 
     * @return string
     */
    public function getComponent(): string;

    /**
     * Get element type
     * 
     * @return string
     */
    public function getElementType(): string;

    /**
     * Get Javascript Element
     * 
     * @return string
     */
    public function getJsElement(): string;

    /**
     * Get property
     * 
     * @param string $key
     * @return mixed
     */
    public function getProperty(string $key);

    /**
     * Get properties
     * 
     * @return array
     */
    public function getProperties(): array;

    /**
     * Get config
     * 
     * @param string $key optional 
     * @return mixed
     */
    public function getConfig($key = null);

    /**
     * Get element type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get element name key
     *
     * @return string
     */
    public function getNameKey(): string;

    /**
     * Get the element name in bracket notation
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get element key
     */
    public function getKey(): string;

    /**
     * Get the element table column name
     * 
     * @return string
     */
    public function getColumnName(): string;

    /**
     * Get the element table column type
     * 
     * @return string
     */
    public function getColumnType(): string;

    /**
     * Get element id
     *
     * @return string
     */

    public function getId(): string;

    /**
     * Get element label property
     *
     * @return mixed
     */
    public function getLabelProperty();

    /**
     * Get element label
     *
     * @return Label | bool
     */
    public function getLabel(): Label | bool;

    /**
     * Get element input before
     *
     * @return mixed
     */
    public function getBefore();

    /**
     * Get element input after
     *
     * @return mixed
     */

    public function getAfter();

    /**
     * Get wrapper
     *
     * @param string $key default 'wrapper'
     * @return array
     */
    public function getWrapper(string $key = 'wrapper'): array;

    /**
     * Get element attributes
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Has element view only
     * 
     * @return bool
     */
    public function hasViewOnly(): bool;

    /**
     * Modify element data before element set data
     * 
     * @param mixed $data
     * @return mixed 
     */
    public function modifyData($data): mixed;

    /**
     * Set element data
     * 
     * @param mixed $data
     * @return void
     */
    public function setData($data): void;

    /**
     * Get element data
     * 
     * @return mixed
     */
    public function getData();

    /**
     * Get the element default value
     * 
     * @return mixed
     */
    public function getValue();

    /**
     * Transforms element to array
     *
     * @param string $side frontend|backend
     * @return array
     */
    public function toArray(string $side = 'frontend'): array;

    /**
     * Determine if element should be validated
     *
     * @return bool
     */
    public function shouldValidate(): bool;

    /**
     * Init element rules
     * 
     * @return void
     */
    public function initRules(): void;

    /**
     * Return rules for side
     *
     * @param string $side
     * @return mixed
     */
    public function getRules(string $side = 'backend');

    /**
     * Determine if element has rules
     *
     * @param string $side
     * @return bool
     */
    public function hasRules(string $side = 'backend'): bool;

    /** 
     * Get element validation messages
     * 
     * @return array
     */
    public function getMessages(): array;

    /**
     * Get Validator
     *
     * @return Validator
     */
    public function getValidator();

    /**
     * Element data to validation format
     *
     * @return array
     */
    public function toValidationData(): array;


    /**
     * Element rules to validation format
     * 
     * @return array
     */
    public function toValidationRules(): array;

    /**
     * Element attributes to validation
     * 
     * @return array
     */
    public function toValidationAttributes(): array;

    /**
     * Element validation message
     * 
     * @param string|object $rule
     * @return void | string
     */
    public function toValidationMessage($rule);

    /**
     * Validate element
     *
     * @param array $messages
     * @return void
     */
    public function validate(array $messages = []);

    /**
     * Check is invalid
     *
     * @return bool
     */
    public function isInvalid(): bool;

    /**
     * Get the value of errors
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Get element error property
     *
     * @return mixed
     */
    public function getErrorProperty();

    /**
     * Get element error
     *
     * @return Error | bool
     */
    public function getError(): Error | bool;

    /**
     * Load value to entity
     *
     * @param Illuminate\Database\Eloquent\Model $entity
     * @return array
     */
    public function load($entity): array;

    /**
     * Fill value to entity from data
     *
     * @param Illuminate\Database\Eloquent\Model $entity
     * @param array $data
     * @param bool $emptyOnNull
     * @return void
     */
    public function fill($entity, array $data, $emptyOnNull = true);

    /**
     * Empty element's value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target);
}
