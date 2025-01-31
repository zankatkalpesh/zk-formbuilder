<?php

namespace Zk\FormBuilder\Contracts\Database;

interface Database
{
    /**
     * Set the value of form
     *
     * @param Zk\FormBuilder\Contracts\Form $form
     * @return self
     */
    public function setForm($form);

    /**
     * Get the value of entity
     * 
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getEntity();

    /**
     * Find entity by key
     *
     * @param integer $key
     * @return object
     */
    public function find($key);

    /**
     * Return data of an entity
     *
     * @param integer $key
     * @return array
     */
    public function load($key): array;

    /**
     * Insert data
     *
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function insert($data);

    /**
     * Update data
     *
     * @param array $data
     * @param integer $key
     * @param bool $emptyOnNull
     * @return Illuminate\Database\Eloquent\Model
     */
    public function update($data, $key, $emptyOnNull = true);
}
