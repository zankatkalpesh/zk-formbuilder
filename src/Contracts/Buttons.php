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
     * @param string $key optional 
     * @return mixed
     */
    public function getConfig($key = null);

    /**
     * Get buttons position
     * 
     * @return string
     */

    public function getPosition(): string;

    /**
     * Check if buttons has position
     *
     * @param string $position
     * @return bool
     */
    public function hasPosition($position): bool;

    /**
     * Get wrapper
     *
     * @return array
     */
    public function getWrapper(): array;

    /**
     * Renders form DOM element
     *
     * @param string $position
     * @param string $view
     * @return mixed
     */
    public function render($position = null, string $view = 'formbuilder::buttons');

    /**
     * Transforms buttons to array
     * 
     * @return array
     */
    public function toArray(): array;
}
