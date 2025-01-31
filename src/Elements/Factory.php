<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

class Factory
{
	/**
	 * Make new Element
	 * 
	 * @param array $field
	 * @param string $name
	 * @param Element | Form $parent
	 * @param array $properties
	 * @param string $configPath
	 * @return Element
	 */
	public function make($field, $name, $parent, $properties, $configPath = 'zklwa.formbuilder')
	{
		$type = $field['type'] ?? 'text';

		// $name = $field['name'] ?? $name;
		$field['name'] = $name;

		$element = $field['element'] ?? $this->getClass($type, $configPath);

		return app()->makeWith($element, compact('field', 'parent', 'properties', 'configPath'));
	}

	/**
	 * Returns element class
	 *
	 * @param string $type
	 * @param string $configPath
	 * @return string
	 */
	protected function getClass($type, $configPath)
	{
		$elements = config($configPath . '.elements');

		if (!empty($elements) && array_key_exists($type, $elements)) {
			return $elements[$type];
		}

		// Check if the element exists in the default elements
		$element = __NAMESPACE__ . '\\' . str_replace('-', '', ucwords($type, '-')) . 'Element';
		if (!class_exists($element)) {
			$element = __NAMESPACE__ . '\\' . 'Element';
		}

		return $element;
	}
}
