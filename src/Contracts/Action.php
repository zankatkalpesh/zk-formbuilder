<?php

namespace Zk\FormBuilder\Contracts;

interface Action
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
     * Get action text before
     * 
     * @return mixed
     */
    public function getBefore();

    /**
     * Get action text after
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
     * Get action text
     * 
     * @return string
     */
    public function getText(): string;

    /**
     * Get action html tag name
     * 
     * @return string
     */
    public function getTagName(): string;

    /**
     * Get action name
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Get action id
     * 
     * @return string
     */
    public function getId(): string;

    /**
     * Get action attributes
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
     * Transforms action to array
     * 
     * @return array
     */
    public function toArray(): array;
}
