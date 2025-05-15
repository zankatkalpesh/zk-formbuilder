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
     * Get Javascript Element
     * 
     * @return string
     */
    public function getJsElement(): string;

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
     * Transforms label to array
     * 
     * @return array
     */
    public function toArray(): array;
}
