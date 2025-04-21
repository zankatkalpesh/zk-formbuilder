<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Helpers;

use Zk\FormBuilder\Traits\GeneralMethods;

class WrapperBuilder
{
    use GeneralMethods;

    /**
     * Tag to build the wrapper
     *
     * @var string
     */
    public $tag = 'div';

    /**
     * Data to build the wrapper
     *
     * @var array
     */

    protected $data = [];

    /**
     * Replace the data to build the wrapper
     * 
     * @var array
     */
    protected $replaceData = [];

    /**
     * Extra value to add in attributes
     *
     * @var array
     */
    protected $extraValue = [];

    /**
     * Create a new instance
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /** 
     * Set the replace data to build the wrapper
     *
     * @param array | string $data
     * @param string $value optional
     * @return self
     */
    public function replace(array | string $data, string $value = ''): self
    {
        if (is_array($data)) {
            $this->replaceData = array_merge($this->replaceData, $data);
        } else {
            $this->replaceData[$data] = $value;
        }

        return $this;
    }

    /**
     * Set the data to build the wrapper
     *
     * @param array $data
     * @return self
     */
    public function set(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Add the extra value in attributes to the wrapper
     * 
     * @param array | string $data, string $value optional
     * @return self
     */
    public function add(array | string $data, string $value = ''): self
    {
        if (is_array($data)) {
            $this->extraValue = array_merge($this->extraValue, $data);
        } else {
            $this->extraValue[$data] = $value;
        }

        return $this;
    }

    /**
     * Build the wrapper
     *
     * @return array
     */
    public function build(): array
    {
        $output = [];
        // Check if the data is empty
        if (empty($this->data)) {
            return $output;
        }
        // Build wrapper tag
        $this->buildTag($this->data, $output);

        // Return the output
        return $output;
    }

    /**
     * Build the tag
     *
     * @param array $tagData
     * @param array $output
     * @return void
     */
    private function buildTag(array $tagData, array &$output)
    {
        if (!isset($tagData['tag'])) {
            $tagData['tag'] = $this->tag;
        }

        $attributes = [];
        // Extract class attribute
        if (!empty($tagData['class'])) {
            $attributes[] = 'class="' . $tagData['class'] . '"';
        }
        // Extract other attributes here
        if (!empty($tagData['attributes'])) {
            if (is_array($tagData['attributes'])) {
                foreach ($tagData['attributes'] as $key => $value) {
                    $attributes[] = (is_numeric($key)) ? trim((string) $value) : $key . '="' . trim((string) $value) . '"';
                }
            } else {
                $attributes[] = $tagData['attributes'];
            }
        }

        $attributes = implode(' ', $attributes);

        // Add extra attributes
        if (!empty($this->extraValue)) {
            foreach ($this->extraValue as $key => $value) {
                // Check if the key in the attributes
                if (strpos($attributes, $key) !== false) {
                    // Replace preg match add value
                    $attributes = preg_replace('/' . $key . '="([^"]+)"/', $key . '="$1 ' . $value . '"', $attributes);
                }
            }
        }

        // Replace the data
        $attributes = $this->replacePattern($attributes, $this->replaceData);

        // Build the tag
        $output[] = [
            'tag' => $tagData['tag'],
            'before' => $tagData['before'] ?? '',
            'after' => $tagData['after'] ?? '',
            'attributes' => $attributes,
        ];
        if (isset($tagData['children'])) {
            $this->buildTag($tagData['children'], $output);
        }
    }
}
