<?php

declare(strict_types=1);

namespace Zk\FormBuilder;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;

use Zk\FormBuilder\Traits\GeneralMethods;
use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Elements\Factory;
use Zk\FormBuilder\Validation\Validator;
use Zk\FormBuilder\Database\Database;
use Zk\FormBuilder\Contracts\Element;
use Zk\FormBuilder\Contracts\Buttons;
use Zk\FormBuilder\Contracts\Validation\Validator as ValidatorContract;

class Form
{
	use GeneralMethods;

	/**
	 * Form id
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Name of form
	 *
	 * @var string
	 */
	public $name;

	/**
	 * field of form
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Name of model class
	 * 
	 * Eg: App\User::class
	 * 
	 * @var string
	 */
	public $model;

	/**
	 * Name of primary key
	 *
	 * @var string
	 */
	public $primaryKey = 'id';

	/**
	 * Key of current entity
	 *
	 * @var integer
	 */
	protected $key;

	/**
	 * Form prefix
	 * 
	 * @var string
	 */
	public $prefix;

	/**
	 * Class override
	 *
	 * @var array
	 */
	public $class;

	/**
	 * Form attributes
	 *
	 * @var array
	 */
	public $attributes;

	/**
	 * Form elements view only
	 * 
	 * @var bool
	 */
	public $viewOnly = false;

	/**
	 * Form before render
	 *
	 * @var string
	 */
	public $beforeRender;

	/**
	 * Form after render
	 *
	 * @var string
	 */
	public $afterRender;

	/**
	 * Form before
	 *
	 * @var string
	 */
	public $before;

	/**
	 * Form after
	 *
	 * @var string
	 */
	public $after;

	/** 
	 * Form wrapper
	 * 
	 * Default: config('zklwa.formbuider.form.wrapper')
	 * 
	 * @var array
	 */
	public $wrapper;

	/**
	 * Properties for the form
	 * 
	 * @var array
	 */
	public $properties = [];

	/**
	 * Form configuration file path
	 *
	 * @var string
	 */
	public $config;

	/**
	 * Form should have files
	 *
	 * @var bool
	 */
	public $files = false;

	/**
	 * Form should have autocomplete
	 *
	 * @var bool
	 */
	public $autocomplete = false;

	/**
	 * Overwrite default validation messages
	 *
	 * Eg.:
	 * [
	 * 	'required' => 'This field is required'
	 * ]
	 * 
	 * Default: config('zklwa.formbuider.messages')
	 * 
	 * @var bool
	 */
	public $messages = [];

	/**
	 * Form buttons
	 *
	 * Default: config('zklwa.formbuider.form.buttons')
	 * 
	 * @var array
	 */
	public $buttons;

	/**
	 * Entpoint to where the form will be submitted
	 * 
	 * Default: config('zklwa.formbuider.form.action')
	 *
	 * @var string
	 */
	public $action;

	/**
	 * Method how the form should be submitted
	 * 
	 * Default: config('zklwa.formbuider.form.method')
	 *
	 * @var string
	 */
	public $method;

	/**
	 * Auth guard to use
	 *
	 * @var string
	 */
	public $guard;

	/**
	 * CSRF token should be added to the form
	 *
	 * @var bool
	 */
	public $csrf = true;

	/**
	 * Determine if form is invalid
	 *
	 * @var bool
	 */
	protected $invalid = false;

	/**
	 * Determine if form has been validated
	 *
	 * @var bool
	 */
	protected $validated = false;

	/**
	 * Current data
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Current elements
	 *
	 * @var Zk\FormBuilder\Contracts\Element[]
	 */
	protected $elements = [];

	/**
	 * Final output data.
	 * 
	 * @var array
	 */
	protected ?array $output = null;

	/**
	 * Meta data
	 *
	 * @var array
	 */
	protected $metaData = [];

	/**
	 * Form constructor
	 *
	 * @param WrapperBuilder $wrapperBuilder
	 * @param Factory $elementFactory
	 * @param Validator $validator
	 * @param Database $database
	 * @param array $args
	 */
	public function __construct(
		protected WrapperBuilder $wrapperBuilder,
		protected Factory $elementFactory,
		protected Validator $validator,
		protected Database $database,
		array $args = [],
		?callable $callback = null
	) {

		if (!empty($args)) {
			foreach ($args as $key => $value) {
				$methodName = 'set' . str_replace('_', '', ucwords($key, '_')); // Convert to camelCase for setter
				if (method_exists($this, $methodName)) {
					$this->$methodName($value);
				} else {
					$this->properties[$key] = $value;
				}
			}
		}

		if ($callback) {
			$callback($this);
		}

		if (method_exists($this, 'init')) {
			app()->call([$this, 'init']);
		}

		$validator->setForm($this);

		$database->setForm($this);

		$this->setFields();

		$this->makeElements();
	}

	/**
	 * Set field
	 *
	 * @return void
	 */
	protected function setFields($fields = null)
	{
		$this->fields = $fields ?? $this->fields;

		if ($this->fields === null) {
			if (method_exists($this, 'fields')) {
				$this->fields = app()->call([$this, 'fields']);
			}
		}
	}

	/**
	 * Make form elements
	 *
	 * @return void
	 */
	private function makeElements()
	{
		if (empty($this->fields)) {
			throw new \Exception('Form field is required');
		}

		foreach ($this->fields as $name => $field) {
			if (is_numeric($name)) {
				$name = $field['name'];
			}
			$name = ($this->getPrefix() ? $this->getPrefix() . '.' : '') . $name;

			$elements = $this->elementFactory->make(
				$field,
				$name,
				$this,
				$this->getProperties(),
				$this->getConfigPath()
			);

			$this->elements[] = $elements;
		}
	}

	/**
	 * Get field of form
	 * 
	 * @return Element[]
	 */
	public function getFields()
	{
		return $this->elements;
	}

	/**
	 * Get property
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function getProperty($key)
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
	 * Set properties
	 * 
	 * @param array $properties
	 * @return Form
	 */
	public function setProperties(array $properties)
	{
		$this->properties = array_merge($this->properties, $properties);

		return $this;
	}

	/** 
	 * Get config path
	 * 
	 * @return string
	 */
	public function getConfigPath(): string
	{
		return $this->config ?? 'formbuilder';
	}

	/**
	 * Set view only
	 * 
	 * @param bool $viewOnly
	 * @return Form
	 */
	public function setViewOnly(bool $viewOnly)
	{
		$this->viewOnly = $viewOnly;

		return $this;
	}

	/**
	 * Has Form element view only
	 * 
	 * @return bool
	 */
	public function hasViewOnly(): bool
	{
		return $this->viewOnly;
	}

	/**
	 * Form should have model
	 *
	 * @return bool
	 */
	public function hasModel(): bool
	{
		return !empty($this->model);
	}

	/**
	 * Get config
	 * 
	 * @return array
	 */

	private function getConfig($key = null)
	{
		$configPath = $this->getConfigPath();

		$config = config($configPath);
		if ($key) {
			return Arr::get($config, $key);
		}

		return $config;
	}

	/**
	 * Get form before render
	 * 
	 * @return mixed
	 */
	public function getBeforeRender()
	{
		$beforeRender = $this->beforeRender ?? null;

		if (empty($beforeRender)) {
			$beforeRender = $this->getConfig('form.beforeRender');
		}

		if (is_callable($beforeRender)) {
			$beforeRender = call_user_func($beforeRender, $this);
		}

		return $beforeRender;
	}

	/**
	 * Get form after render
	 * 
	 * @return mixed
	 */
	public function getAfterRender()
	{
		$afterRender = $this->afterRender ?? null;

		if (empty($afterRender)) {
			$afterRender = $this->getConfig('form.afterRender');
		}

		if (is_callable($afterRender)) {
			$afterRender = call_user_func($afterRender, $this);
		}

		return $afterRender;
	}

	/**
	 * Get form element's input before
	 * 
	 * @return mixed
	 */
	public function getBefore()
	{
		$before = $this->before ?? null;

		if (empty($before)) {
			$before = $this->getConfig('form.before');
		}

		if (is_callable($before)) {
			$before = call_user_func($before, $this);
		}

		return $before;
	}

	/**
	 * Get form element's input after
	 * 
	 * @return mixed
	 */
	public function getAfter()
	{
		$after = $this->after ?? null;

		if (empty($after)) {
			$after = $this->getConfig('form.after');
		}

		if (is_callable($after)) {
			$after = call_user_func($after, $this);
		}

		return $after;
	}

	/**
	 * Retrieving buttons
	 *
	 * @return Buttons
	 */
	public function getButtons()
	{
		if ($this->buttons === null) {
			if (method_exists($this, 'buttons')) {
				$this->buttons = app()->call([$this, 'buttons']);
			}
		}

		if ($this->buttons != null && !isset($this->buttons['actions'])) {
			$this->buttons = ['actions' => $this->buttons];
		}

		$this->buttons['actions'] = $this->buttons['actions'] ?? $this->getConfig('form.buttons.actions');
		$this->buttons['wrapper'] = $this->buttons['wrapper'] ?? $this->getConfig('form.buttons.wrapper');
		$this->buttons['actionConfig'] = $this->buttons['actionConfig'] ?? $this->getConfig('form.buttons.actionConfig');
		$this->buttons['position'] = $this->buttons['position'] ?? $this->getConfig('form.buttons.position');
		$this->buttons['component'] = $this->buttons['component'] ?? $this->getConfig('form.buttons.component');

		return app()->makeWith(__NAMESPACE__ . '\\Elements\\Buttons', ['form' => $this, 'configPath' => $this->getConfigPath()]);
	}

	/**
	 * Retrieving messages
	 *
	 * @return array
	 */
	public function getMessages(): array
	{
		$messages = $this->getConfig('messages');

		if (!empty($this->messages)) {
			$messages = array_merge($messages, $this->messages);
		}

		return $messages ?? [];
	}

	/**
	 * Set method
	 *
	 * @return Form
	 */
	public function setMethod($method)
	{
		$this->method = strtoupper($method);

		return $this;
	}

	/**
	 * Get method
	 *
	 * @return string
	 */
	public function getMethod(): string
	{
		return strtoupper($this->method ?? ($this->getConfig('form.method') ?? 'POST'));
	}

	/**
	 * Set model
	 *
	 * @param Illuminate\Database\Eloquent\Model $model
	 * @return Form
	 */
	public function setModel($model)
	{
		$this->model = $model;

		return $this;
	}

	/**
	 * Set action
	 *
	 * @param string $action
	 * @return Form
	 */
	public function setAction($action)
	{
		$this->action = $action;

		return $this;
	}

	/**
	 * Get action
	 *
	 * @return string
	 */
	public function getAction(): string
	{
		$action = $this->action ?? $this->getConfig('form.action');

		return ($action && Route::has($action)) ? route($action) : $action;
	}

	/**
	 * Form should have files
	 *
	 * @return bool
	 */
	public function hasFiles(): bool
	{
		return $this->files;
	}

	/**
	 * Generate the form key
	 *
	 * @return string
	 */
	public function getFormKey(): string
	{
		return encrypt((new \ReflectionClass($this))->getName());
	}

	/**
	 * Get form Name
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name ?? (new \ReflectionClass($this))->getShortName();
	}

	/**
	 * Get form id
	 *
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id ?? $this->getName();
	}

	/**
	 * Get form prefix
	 *
	 * @return string | null
	 */
	public function getPrefix(): ?string
	{
		$prefix = $this->prefix ?? $this->getConfig('form.prefix');

		if ($prefix) {
			// Replace the data
			$replaceData = $this->getReplaceData();
			$prefix = $this->replacePattern($prefix, $replaceData);
		}

		return $prefix;
	}

	/**
	 * Renders form DOM element
	 *
	 * @param string $view
	 * @return \Illuminate\Contracts\View\View
	 */
	public function render(string $view = 'formbuilder::form')
	{
		return view($view, ['form' => $this]);
	}

	/**
	 * Get wrapper
	 *
	 * @return array
	 */
	public function getWrapper(): array
	{
		$this->wrapper = $this->wrapper ?? $this->getConfig('form.wrapper');
		if (empty($this->wrapper)) {
			$this->wrapper = [];
		}

		// Replace the data
		$replaceData = $this->getConfig('form.replace') ?? [];
		$replaceData['{name}'] = $this->getName();
		$replaceData['{id}'] = $this->getId();
		// Add Error class to element
		$replaceData['{errorClass}'] = '';
		if ($this->isInvalid()) {
			$errorClass = $this->getConfig('form.wrapper.errorClass') ?? '';
			$replaceData['{errorClass}'] = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
		}

		// Wrapper Builder
		$wBuilder = clone $this->wrapperBuilder;
		return $wBuilder
			->replace($replaceData)
			->set($this->wrapper)
			->build();
	}

	/**
	 * Get form attributes
	 * 
	 * @return array
	 */
	public function getAttributes(): array
	{
		$class = $this->class ?? $this->getConfig('form.class');
		$attributes = [
			'class' => $class ?? '',
			'name' => $this->getName(),
			'id' => $this->getId(),
			'autocomplete' => $this->autocomplete ? 'on' : 'off',
		];
		if ($this->hasFiles()) {
			$attributes['enctype'] = 'multipart/form-data';
		}

		$this->attributes = $this->attributes ?? (array)$this->getConfig('form.attributes');

		$attributes = array_merge($this->attributes, $attributes);

		// Replace the data
		$replaceData = $this->getReplaceData();
		// Add Error class to element
		$replaceData['{errorClass}'] = '';
		if ($this->isInvalid()) {
			$errorClass = $this->getConfig('form.errorClass') ?? '';
			$replaceData['{errorClass}'] = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
		}
		$attributes = $this->replacePattern($attributes, $replaceData);

		return $attributes;
	}

	/**
	 * Replace Data
	 * 
	 * @return array
	 */
	protected function getReplaceData()
	{
		// Replace the data
		$replaceData = $this->getConfig('form.replace') ?? [];
		$replaceData['{name}'] = $this->getName();
		$replaceData['{id}'] = $this->getId();
		// Add Error class to element
		$replaceData['{errorClass}'] = '';
		if ($this->isInvalid()) {
			$errorClass = $this->getConfig('form.errorClass') ?? '';
			$replaceData['{errorClass}'] = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
		}

		return $replaceData;
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
		foreach ($this->getFields() as $field) {
			$fields[] = $field->toArray($side);
		}

		return $fields;
	}

	/**
	 * Set data
	 *
	 * @param array $data
	 * @return void
	 */
	public function setData($data)
	{
		// Modify each field data before field set data
		foreach ($this->getFields() as $field) {
			$data = $field->modifyData($data);
		}

		// Assign data to form
		$this->data = $data;

		// Assign data to each field
		foreach ($this->getFields() as $field) {
			$field->setData($this->data);
		}
	}

	/**
	 * Get form data
	 *
	 * @param string|null $key
	 * @return mixed
	 */
	public function getData($key = null): mixed
	{
		if ($key) {
			return Arr::get($this->data, $key);
		}

		return $this->data;
	}

	/**
	 * Get Validator
	 *
	 * @return ValidatorContract
	 */
	public function getValidator()
	{
		return $this->validator;
	}

	/**
	 * Validate form
	 *
	 * @return void
	 */
	public function validate()
	{
		$this->validated = true;

		$messages = $this->getMessages();

		$this->validator->validate($messages);

		if ($this->validator->fails()) {
			$this->invalid = true;
		}
	}

	/**
	 * Form is invalid
	 *
	 * @return bool
	 */
	public function isInvalid(): bool
	{
		return $this->invalid;
	}

	/**
	 * Form is validated
	 *
	 * @return bool
	 */
	public function isValidated(): bool
	{
		return $this->validated;
	}

	/**
	 * Get form errors
	 * 
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->validator->getErrors()->toArray();
	}

	/**
	 * Load data to form
	 *
	 * @param array|integer|null $key
	 * @return bool
	 */
	public function load($key = null): bool
	{
		if ($key === null && $this->key === null) {
			return false;
		}

		if ($key === null) {
			$key = $this->key;
		}

		if (is_array($key)) {
			$this->setData($key);
			return true;
		}

		$data = $this->database->load($key);

		if (!empty($data)) {
			$this->setData($data);
			$this->setKey($key);
			return true;
		}

		return false;
	}

	/**
	 * Return the entity based on current key
	 *
	 * @return object
	 */
	public function getEntity()
	{
		return (!empty($this->key)) ? $this->database->find($this->key) : null;
	}

	/**
	 * Set the value of key
	 *
	 * @param mixed $key
	 * @return void
	 */
	public function setKey($key)
	{
		$this->key = $key;

		// Set primary key value
		$this->properties['primaryId'] = $this->key;
	}

	/**
	 * Get the value of key encrypted
	 *
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->key ? encrypt($this->key) : null;
	}

	/**
	 * Set meta data
	 * 
	 * @param array $metaData
	 * @return Form
	 */
	public function setMetaData(array $metaData)
	{
		$this->metaData = array_merge($this->metaData, $metaData);

		return $this;
	}

	/**
	 * Get meta data
	 * 
	 * @return array
	 */
	public function getMetaData(): array
	{
		return $this->metaData;
	}

	/**
	 * Get encrypted meta data
	 * 
	 * @return string | null
	 */
	public function getEncryptedMetaData()
	{
		return (!empty($this->metaData)) ? encrypt($this->metaData) : null;
	}

	/**
	 * Save data
	 *
	 * @return mixed
	 */
	public function save($metaData = [])
	{
		$this->setMetaData($metaData);

		return (!empty($this->key)) ? $this->update() : $this->insert();
	}

	/**
	 * Insert new data
	 *
	 * @return object
	 */
	private function insert()
	{
		$entity = $this->database->insert($this->data);

		$this->setKey($entity[$this->primaryKey] ?? null);

		return $entity;
	}

	/**
	 * Update data
	 *
	 * @return object
	 */
	private function update()
	{
		$entity = $this->database->update($this->data, $this->key);

		$this->setKey($entity[$this->primaryKey] ?? null);

		return $entity;
	}

	/**
	 * Transforms form to Array
	 * 
	 * @param string $side frontend|backend
	 * @param array $exclude keys to exclude
	 * @return array
	 */
	public function toArray($side = 'frontend', $exclude = []): array
	{
		if ($this->output !== null) {
			return $this->output;
		}

		$this->output = [
			'prefix' => $this->getPrefix(),
			'key' => $this->getKey(),
			'metaData' => $this->getEncryptedMetaData(),
			'frmKey' => $this->getFormKey(),
			'action' => $this->getAction(),
			'method' => $this->getMethod(),
			'fields' => $this->getFieldsToArray($side),
			'wrapper' => $this->getWrapper(),
			'attributes' => $this->getAttributes(),
			'buttons' => $this->getButtons()->toArray(),
			'messages' => $this->getMessages(),
			'validated' => $this->isValidated(),
			'invalid' => $this->isInvalid(),
			'errors' => $this->isValidated() ? $this->getErrors() : [],
			'hasFiles' => $this->hasFiles(),
			'beforeRender' => $this->getBeforeRender(),
			'afterRender' => $this->getAfterRender(),
			'before' => $this->getBefore(),
			'after' => $this->getAfter(),
			'csrf' => $this->csrf,
			'csrf_token' => csrf_token(),
			'data' => $this->data,
			'properties' => $this->getProperties(),
			'viewOnly' => $this->hasViewOnly(),
		];

		foreach ($exclude as $key) {
			unset($this->output[$key]);
		}

		return $this->output;
	}

	/**
	 * Render form as json.
	 * 
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function renderJson($side = 'frontend', $exclude = [])
	{
		return response()->json($this->toArray($side, $exclude));
	}

	/**
	 * Transforms form to JSON
	 * 
	 * @param string $side frontend|backend
	 * @param array $exclude keys to exclude
	 * @return string
	 */
	public function toJson($side = 'frontend', $exclude = []): string
	{
		return addslashes(json_encode($this->toArray($side, $exclude)));
	}
}
