<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Validation;

use Zk\FormBuilder\Contracts\Validation\Validator as ValidatorContract;
use Zk\FormBuilder\Contracts\Element;
use Illuminate\Contracts\Validation\Validator as IlluminateValidator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class Validator implements ValidatorContract
{
    /**
     * Validator instance
     *
     * @var IlluminateValidator
     */
    public $validator;

    /**
     * Form instance
     *
     * @var Zk\FormBuilder\Contracts\Form
     */
    private $form;

    /**
     * Elements to validate
     *
     * @var Element[]
     */
    protected $elements = [];

    /**
     * Element to data mapping
     *
     * @var array
     */
    protected $elementData = [];

    /**
     * Element to rules mapping
     *
     * @var array
     */
    protected $elementRules = [];

    /**
     * Rules where wildcards should be kept
     *
     * @var array
     */
    protected $withWildcards = ['distinct'];

    /**
     * Return new Validation instance
     *
     * @param IlluminateValidator $validator
     */
    public function __construct(IlluminateValidator $validator)
    {
        $this->validator = $validator;

        if (app('validation.presence')) {
            $this->validator->setPresenceVerifier(app('validation.presence'));
        }
    }

    /**
     * Set the value of form
     *
     * @param Zk\FormBuilder\Contracts\Form $form
     * @return self
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Validates element against given data
     *
     * @param Element $element
     * @param array $messages
     * @return void
     */
    public function validate(array $messages)
    {
        // Set messages
        if (!empty($messages)) {
            $this->validator->setCustomMessages($messages);
        }

        foreach ($this->form->getFields() as $element) {
            $element->validate();
        }
    }

    /**
     * Set data for validator
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $name = key($data);
        $data = $data[$name];
        $this->validator->setValue($name, $data);
    }

    /**
     * Set rules for validator
     *
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules)
    {
        $name = key($rules);
        $rules = $rules[$name];
        $this->addRules($rules, $name);
    }

    /**
     * Add rules to validator
     *
     * @param mixed $rules
     * @param string $name
     * @return void
     */
    protected function addRules($rules, $name)
    {
        if (!is_array($rules)) {
            $rules = explode('|', $rules);
        }

        foreach ($rules as $key => $rule) {
            switch ($this->getRuleType($rule, $key)) {
                case 'callable':
                case 'string':
                    $this->addRule($rule, $name);
                    break;

                case 'language':
                    $this->addRule($rule, $name . '.' . $key);
                    break;

                case 'implicit':
                    $this->addImplicitRule(key($rule), $name, $rule[key($rule)]);
                    break;
            }
        }
    }

    /**
     * Add rule to validator
     *
     * @param mixed $rule
     * @param string $name
     * @return void
     */
    protected function addRule($rule, $name)
    {
        if (is_string($rule)) {
            $rule = $this->fillWildcards($rule, $name);
        }

        if (is_array($rule)) {
            $rule = array_map(function ($one) use ($name) {
                return $this->fillWildcards($one, $name);
            }, $rule);
        }

        if (in_array($rule, $this->withWildcards)) {
            $name = $this->addWildcards($name);
        }

        $this->validator->addRules([
            $name => [$rule]
        ]);
    }

    /**
     * Add implicit rule to validator
     *
     * @param string $rule
     * @param string $name
     * @param array|callable $condition
     * @return void
     */
    protected function addImplicitRule($rule, $name, $condition)
    {
        if (is_array($condition)) {
            $field = $this->fillWildcards($condition[0], $name);
            $operator = count($condition) == 3 ? $condition[1] : '=';
            $value = count($condition) == 3 ? $condition[2] : $condition[1];

            $condition = function ($input) use ($field, $operator, $value) {
                return $this->compare(Arr::get($input, $field), $value, $operator);
            };
        }

        if (in_array($rule, $this->withWildcards)) {
            $name = $this->addWildcards($name);
        }

        $this->validator->sometimes($name, $rule, $condition);
    }

    /**
     * Add attribute to validator
     * 
     * @param string | array $attributes
     * @param string $value
     * @return void
     */
    public function addAttributes($attributes, $value = null)
    {
        if (is_array($attributes)) {
            $this->validator->addCustomAttributes($attributes);
        } else {
            $this->validator->addCustomAttributes($attributes, $value);
        }
    }

    /**
     * Add messages to validator
     *
     * @param Element $element
     * @param mixed $rules
     * @param string $name
     * @return void
     */
    public function addMessages(Element $element, $rules, $name)
    {
        if (!is_array($rules)) {
            $rules = explode('|', $rules);
        }
        $rules = array_map(function ($rule) {
            return (is_string($rule)) ? explode(':', $rule)[0] : $rule;
        }, $rules);
        foreach ($rules as $key => $rule) {
            switch ($this->getRuleType($rule, $key)) {
                case 'callable':
                    $this->addCallableMessage($element, $rule, $name);
                    break;

                case 'string':
                    $this->addMessage($element, $rule, $name);
                    break;

                case 'language':
                    $this->addMessage($element, $rule, $name . '.' . $key);
                    break;

                case 'implicit':
                    $this->addMessage($element, key($rule), $name, $rule[key($rule)]);
                    break;
            }
        }
    }

    /**
     * Add message to validator
     *
     * @param Element $element
     * @param string $rule
     * @param string $name
     * @return void
     */
    protected function addMessage($element, $rule, $name)
    {
        $message = $element->toValidationMessage($rule);

        // Check if rule
        if (!is_string($rule) && $rule instanceof ValidationRule || is_object($rule)) {
            $rule = lcfirst((new \ReflectionClass($rule))->getShortName());
        }

        if ($message) {
            $this->validator->setCustomMessages([$name . '.' . $rule => $message]);
        }
    }

    /**
     * Add message to validator
     * 
     * @param Element $element
     * @param Rule $rule
     * @param string $name
     * @return void
     */
    protected function addCallableMessage($element, ValidationRule $rule, $name)
    {
        $message = (new $rule)->message();
        $ruleName = lcfirst((new \ReflectionClass($rule))->getShortName());

        if ($message) {
            $this->validator->setCustomMessages([$name . '.' . $ruleName => $message]);
        }
    }

    /**
     * Fill asterix values with concrete indexes
     *
     * @param string $fillable - string to be filled
     * @param string $fill - string to get indexes from
     * @return string
     */
    protected function fillWildcards($fillable, $fill)
    {
        preg_match('/\.[0-9]/', $fill, $matches);

        if (count($matches) == 0) {
            return $fillable;
        }

        return vsprintf(str_replace('.*', '%s', $fillable), $matches);
    }

    /**
     * Add wildcard instead of the last index
     *
     * @param string $name
     * @return string
     */
    protected function addWildcards($name)
    {
        return preg_replace('/\d+(?!\d+)/', '*', $name);
    }

    /**
     * Compare two values with a given operator
     *
     * @param mixed $first
     * @param mixed $second
     * @param string $operator
     * @return bool
     */
    protected function compare($first, $second, $operator)
    {
        switch ($operator) {
            case "=":
                return $first == $second;
            case "!=":
                return $first != $second;
            case ">=":
                return $first >= $second;
            case "<=":
                return $first <= $second;
            case ">":
                return $first > $second;
            case "<":
                return $first < $second;
        }

        throw new \InvalidArgumentException('Unknown operator:' . $operator);
    }

    /**
     * Determine the type of rule
     *
     * @param mixed $rule
     * @param mixed $key
     * @return string
     */
    protected function getRuleType($rule, $key)
    {
        if (is_numeric($key)) {
            if (is_array($rule)) {
                return 'implicit';
            } elseif ($rule instanceof ValidationRule) {
                return 'callable';
            } else {
                return 'string';
            }
        } else {
            return 'language';
        }
    }

    /**
     * Determine if validation fails
     *
     * @return bool
     */
    public function fails(): bool
    {
        return $this->validator->fails();
    }

    /**
     * Check is attribute invalid
     *
     * @param array|string|null  $attribute
     * @return bool
     */
    public function isInvalid($attribute): bool
    {
        return $this->validator->errors()->has($attribute);
    }

    /**
     * Get all errors for a given attribute
     *
     * @param string $attribute
     * @param  string|null  $format
     * @return array
     */
    public function getError(string $attribute, $format = null): array
    {
        return $this->validator->errors()->get($attribute, $format);
    }

    /**
     * Return the value of errors
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->validator->errors();
    }
}
