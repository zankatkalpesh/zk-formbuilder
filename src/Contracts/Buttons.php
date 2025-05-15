<?php

namespace Zk\FormBuilder\Contracts;

interface Buttons
{
    /**
     * Get Component
     * 
     * @return string
     */
    public function getComponent(): string;

    /**
     * Get Javascript Element
     * 
     * @return string
     */
    public function getJsElement(): string;

    /**
     * Get buttons text before
     * 
     * @return mixed
     */
    public function getBefore();

    /**
     * Get buttons text after
     * 
     * @return mixed
     */
    public function getAfter();

    /**
     * Get config
     * 
     * @param string $key The key to search for within the action configuration.
     * @param string $group The configuration group to search within.
     * @return mixed The configuration value associated with the key, or null if not found.
     */
    public function getConfig($key, $group = null);

    /**
     * Get buttons position
     * 
     * @return string
     */

    public function getPosition(): string;

    /**
     * Get wrapper
     *
     * @return array
     */
    public function getWrapper(): array;

    /**
     * Transforms buttons to array
     * 
     * @return array
     */
    public function toArray(): array;
}
