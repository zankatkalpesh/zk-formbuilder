<?php

namespace Zk\FormBuilder\Contracts;

interface Form
{
    /**
     * Form fields
     *
     * @var array
     */
    public function fields();

    /**
     * Get fields of form
     * 
     * @return Element[]
     */
    public function getFields();

    /**
     * Make Element 
     * 
     * @param array $field
     * @param string $name
     * @param Element | null $parent
     * @return Element | null
     */
    public function makeElement($name, $field, $parent = null);

    /**
     * Get property
     * 
     * @param string $key
     * @return mixed
     */
    public function getProperty($key);

    /**
     * Get properties
     * 
     * @return array
     */
    public function getProperties(): array;

    /** 
     * Get config path
     * 
     * @return string
     */
    public function getConfigPath(): string;

    /**
     * Get config
     * 
     * @param string $key optional 
     * @return mixed
     */
    public function getConfig($key = null);

    /**
     * Has Form element view only
     * 
     * @return bool
     */
    public function hasViewOnly(): bool;

    /**
     * Form should have model
     *
     * @return bool
     */
    public function hasModel(): bool;

    /**
     * Get form element's input before
     * 
     * @return mixed
     */
    public function getBefore();

    /**
     * Get form element's input after
     * 
     * @return mixed
     */
    public function getAfter();

    /**
     * Retrieving buttons
     *
     * @return Buttons
     */
    public function getButtons();

    /**
     * Retrieving messages
     *
     * @return array
     */
    public function getMessages(): array;

    /**
     * Set method
     *
     * @return Form
     */
    public function setMethod($method);

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Set model
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @return Form
     */
    public function setModel($model);

    /**
     * Set action
     *
     * @param string $action
     * @return Form
     */
    public function setAction($action);

    /**
     * Get action
     * 
     * @return string
     */
    public function getAction(): string;

    /**
     * Set form should have files
     * 
     * @param bool $hasFiles
     */
    public function setHasFiles($hasFiles);

    /**
     * Form should have files
     *
     * @return bool
     */
    public function hasFiles(): bool;

    /**
     * Generate the form key
     *
     * @return string
     */
    public function getFormKey(): string;

    /**
     * Get form Name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get form id
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get form prefix
     *
     * @return string | null
     */
    public function getPrefix(): ?string;

    /**
     * Renders form DOM element
     *
     * @param string $view
     * @return \Illuminate\Contracts\View\View
     */
    public function render(string $view = 'formbuilder::form');

    /**
     * Get wrapper
     *
     * @return array
     */
    public function getWrapper(): array;

    /**
     * Get form attributes
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Set data
     *
     * @param array $data
     * @return void
     */
    public function setData($data);

    /**
     * Get form data
     * 
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null): mixed;

    /**
     * Get Validator
     *
     * @return Validation\Validator
     */
    public function getValidator();

    /**
     * Validate form
     *
     * @return void
     */
    public function validate();

    /**
     * Form is invalid
     *
     * @return bool
     */
    public function isInvalid(): bool;

    /**
     * Form is validated
     *
     * @return bool
     */
    public function isValidated(): bool;

    /**
     * Get form errors
     * 
     * @return array
     */
    public function getErrors(): array;

    /**
     * Load data to form
     *
     * @param array|integer|null $key
     * @return bool
     */
    public function load($key = null): bool;

    /**
     * Return the entity based on current key
     *
     * @return object
     */
    public function getEntity();

    /**
     * Set the value of key
     *
     * @param mixed $key
     * @return void
     */
    public function setKey($key);

    /**
     * Get the value of key
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Set meta data
     * 
     * @param array $metaData
     * @return Form
     */
    public function setMetaData(array $metaData);

    /**
     * Get meta data
     * 
     * @return array
     */
    public function getMetaData(): array;

    /**
     * Get encrypted meta data
     * 
     * @return string | null
     */
    public function getEncryptedMetaData();

    /**
     * Save data
     *
     * @return mixed
     */
    public function save();

    /**
     * Transforms form to Array
     * 
     * @param string $side frontend|backend
     * @return array
     */
    public function toArray($side = 'frontend'): array;

    /**
     * Transforms form to JSON
     * 
     * @param string $side frontend|backend
     * @return string
     */
    public function toJson($side = 'frontend'): string;
}
