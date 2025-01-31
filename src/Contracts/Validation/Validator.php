<?php

namespace Zk\FormBuilder\Contracts\Validation;

use Zk\FormBuilder\Contracts\Element;

interface Validator
{

    /**
     * Set the value of form
     *
     * @param Zk\FormBuilder\Contracts\Form $form
     * @return self
     */
    public function setForm($form);

    /**
     * Validate elements against data
     *
     * @param array $messages
     * @return void
     */
    public function validate(array $messages);

    /**
     * Set data for validator
     *
     * @param array $data
     * @return void
     */

    public function setData(array $data);

    /**
     * Set rules for validator
     *
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules);

    /**
     * Add attribute to validator
     * 
     * @param string | array $attributes
     * @param string $value
     * @return void
     */
    public function addAttributes($attributes, $value = null);

    /**
     * Add message for validator
     *
     * @param Element $element
     * @param array $rules
     * @param string $name
     * @return void
     */
    public function addMessages(Element $element, array $rules, $name);

    /**
     * Determine if validation fails
     *
     * @return bool
     */
    public function fails(): bool;

    /**
     * Check is attribute invalid
     *
     * @param array|string|null  $attribute
     * @return bool
     */
    public function isInvalid($attribute): bool;

    /**
     * Get all errors for a given attribute
     *
     * @param string $attribute
     * @param  string|null  $format
     * @return array
     */
    public function getError(string $attribute, $format = null): array;

    /**
     * Get the value of errors
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors();
}
