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
     * Get Javascript Element
     * 
     * @return string
     */
    public function getJsElement(): string;

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
     * Get error position
     * 
     * @return string
     */

    public function getPosition(): string;

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
     * Transforms error to array
     * 
     * @return array
     */
    public function toArray(): array;
}
