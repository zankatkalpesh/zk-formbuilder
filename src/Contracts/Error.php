<?php

namespace Zk\FormBuilder\Contracts;

interface Error
{
    /**
     * Get Component
     * 
     * @return string
     */
    public function getComponent(): string;
    
    /**
     * Get error text before
     * 
     * @return mixed
     */
    public function getBefore();

    /**
     * Get error text after
     * 
     * @return mixed
     */
    public function getAfter();

    /**
     * Get errors
     * 
     * @return array
     */
    public function getErrors(): array;

    /**
     * Get config
     * 
     * @param string $key optional 
     * @return mixed
     */
    public function getConfig($key = null);

    /**
     * Get error position
     * 
     * @return string
     */

    public function getPosition(): string;

    /**
     * Check if error has position
     *
     * @param string $position
     * @return bool
     */
    public function hasPosition($position): bool;

    /**
     * Get error html tag name
     * 
     * @return string
     */
    public function getTagName(): string;

    /**
     * Get error id
     * 
     * @return string
     */
    public function getId(): string;

    /**
     * Get error attributes
     * 
     * @return array
     */
    public function getAttributes(): array;

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
    public function render($position = null, string $view = 'formbuilder::error');

    /**
     * Transforms error to array
     * 
     * @return array
     */
    public function toArray(): array;
}
