<?php

namespace Zk\FormBuilder\Contracts;

interface Label
{
    /**
     * Get Component
     * 
     * @return string
     */
    public function getComponent(): string;
    
    /**
     * Get label text before
     * 
     * @return mixed
     */
    public function getBefore();

    /**
     * Get label text after
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
     * Get label text
     * 
     * @return string
     */
    public function getText(): string;

    /**
     * Get label position
     * 
     * @return string
     */

    public function getPosition(): string;

    /**
     * Check if label has position
     *
     * @param string $position
     * @return bool
     */
    public function hasPosition($position): bool;

    /**
     * Get label html tag name
     * 
     * @return string
     */
    public function getTagName(): string;

    /**
     * Get label id
     * 
     * @return string
     */
    public function getId(): string;

    /**
     * Get label attributes
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
    public function render($position = null, string $view = 'formbuilder::label');

    /**
     * Transforms label to array
     * 
     * @return array
     */
    public function toArray(): array;
}
