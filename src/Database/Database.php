<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Database;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Zk\FormBuilder\Contracts\Database\Database as DatabaseContract;

class Database implements DatabaseContract
{

    /**
     * Database class
     *
     * @var DB
     */
    private $db;

    /**
     * Form instance
     *
     * @var Zk\FormBuilder\Contracts\Form
     */
    private $form;

    /**
     * Entity instance
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $entity;

    /**
     * Creates new database instance
     * 
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Set the value of form
     *
     * @param Zk\FormBuilder\Contracts\Form $form
     * @return self
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get entity instance
     * 
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getEntity()
    {
        return $this->entity = $this->getModel();
    }

    /**
     * Find entity by key
     *
     * @param integer $key
     * @return object
     */
    public function find($key)
    {
        return $this->entity = $this->getModel()->find($key);
    }

    /**
     * Return data of an entity
     *
     * @param integer $key
     * @return array
     */
    public function load($key): array
    {
        $this->find($key);

        return $this->retrive();
    }

    /**
     * Retrive data from elements
     *
     * @return array
     */
    private function retrive(): array
    {
        if ($this->entity === null) {
            return [];
        }

        $entity = clone $this->entity;

        $data = [];

        foreach ($this->form->getFields() as $element) {
            $data = array_merge($data, $element->load($entity));
        }

        return Arr::undot($data);
    }

    /**
     * Create and set new entity
     *
     * @return void
     */
    private function create()
    {
        $this->entity = $this->getModel();
    }

    /**
     * Save entity
     *
     * @return void
     */
    private function save()
    {
        $this->entity->save();
    }

    /**
     * Insert data
     *
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function insert($data)
    {
        $this->db::transaction(function () use ($data) {
            $this->create();
            $this->fill($data);
            $this->fillMetaData();
            $this->save();
        });

        return $this->entity;
    }

    /**
     * Update data
     *
     * @param array $data
     * @param integer $key
     * @param bool $emptyOnNull
     * @return Illuminate\Database\Eloquent\Model
     */
    public function update($data, $key, $emptyOnNull = false)
    {
        $this->db::transaction(function () use ($data, $key, $emptyOnNull) {
            $this->find($key);
            $this->fill($data, $emptyOnNull);
            $this->fillMetaData($emptyOnNull);
            $this->save();
        });

        return $this->entity;
    }

    /**
     * Fill elements with data
     *
     * @param array $data
     * @param bool $emptyOnNull
     * @return void
     */
    private function fill($data, $emptyOnNull = true)
    {
        foreach ($this->form->getFields() as $element) {
            $element->fill($this->entity, $data, $emptyOnNull);
        }
    }

    /**
     * Fill meta data
     *
     * @param bool $emptyOnNull
     * @return void
     */
    private function fillMetaData($emptyOnNull = true)
    {
        $metaData = $this->form->getMetaData() ?? [];
        foreach ($metaData as $key => $value) {
            if (is_array($value)) {
                $this->entity[$key] = json_encode($value);
            } else {
                $this->entity[$key] = $emptyOnNull ? ($value ?? null) : $value;
            }
        }
    }

    /**
     * Return model instance
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    private function getModel()
    {
        return app($this->form->model);
    }
}
