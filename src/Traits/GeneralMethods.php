<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Traits;

/**
 * General methods
 */
trait GeneralMethods
{

	/**
	 * Print element's attributes
	 * 
	 * @param array $attributes
	 * @param array $exclude // The attribute to exclude from the output.
	 * @return string
	 */
	public function printAttributes(array $attributes = [], $exclude = []): string
	{
		$attributes = (!empty($attributes)) ? $attributes : $this->getAttributes();
		$htmlAttributes = [];
		foreach ($attributes as $key => $value) {
			if (in_array($key, $exclude)) continue;
			$htmlAttributes[] = (is_numeric($key)) ? trim((string) $value) : $key . '="' . trim((string) $value) . '"';
		}
		return implode(' ', $htmlAttributes);
	}

	/**
	 * Replace pattern
	 * 
	 * @param mixed $data
	 * @param array $replaceData
	 * @return mixed
	 */
	public function replacePattern(mixed $data, array $replaceData): mixed
	{
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = $this->replacePattern($value, $replaceData);
			}
		} else {
			$data = str_replace(array_keys($replaceData), array_values($replaceData), (string) $data);
		}
		return $data;
	}

	/**
	 * Convert string to bracket notation
	 * 
	 * @param string $str
	 * @param string $separator // Default is '.'
	 * @return string
	 */

	public function toBracketNotation(string $str, string $separator = '.'): string
	{
		$suffix = '';
		if (!str_contains($str, $separator)) return $str; // No $separator, return the original string
		if (str_ends_with($str, '[]')) {
			$str = substr($str, 0, -2);
			$suffix = '[]';
		}
		$parts = explode($separator, $str);
		$firstPart = array_shift($parts);
		return $firstPart . '[' . implode('][', $parts) . ']' . $suffix;
	}

	/**
	 * Convert string to human readable
	 * 
	 * @param string $str
	 * @return string
	 */

	public function toHumanReadable(string $str): string
	{
		$str = ucwords($str, '-');
		$str = ucwords($str, '_');

		return str_replace(['-', '_'], ' ', $str);
	}
}
