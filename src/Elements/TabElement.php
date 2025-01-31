<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Illuminate\Support\Arr;
use Zk\FormBuilder\Helpers\WrapperBuilder;

class TabElement extends Element
{
    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::template.tab';

    /**
     * Element type
     *
     * @var string
     */
    public $elementType = 'tab';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkTabElement';

    /**
     * Element tabs
     *
     * @var array
     */
    protected $tabs = [];

    /**
     * Return new Element instance
     *
     * @param array $field
     * @param Element | Form $parent
     * @param array $properties
     * @param string $configPath
     * @param Factory $elementFactory
     * @param WrapperBuilder $wrapperBuilder
     */
    public function __construct(
        $field,
        protected $parent,
        $properties,
        $configPath,
        protected Factory $elementFactory,
        protected WrapperBuilder $wrapperBuilder
    ) {
        parent::__construct($field, $parent, $properties, $configPath, $elementFactory, $wrapperBuilder);

        $this->setTabs();
    }

    /**
     * Set tabs
     *
     * @return void
     */
    protected function setTabs(): void
    {
        $tabs = $this->field['tabs'] ?? [];

        if (is_callable($tabs)) {
            $tabs = call_user_func($tabs, $this);
        }

        foreach ($tabs as $name => $tab) {
            if (is_numeric($name)) {
                $name = $tab['name'];
            }
            $tab['name'] = $name;
            $this->tabs[] = $this->makeTab($tab);
        }
    }

    /**
     * Make tab form elements
     *
     * @param array $tab
     * @return array
     */
    public function makeTab(array $tab): array
    {
        $tabName = $this->name;
        // Remove last element name from group name if not parent and has dot in name
        if (!$this->isParent() && strpos($tabName, ".") !== false) {
            $nameArr = explode('.', $tabName);
            $tabName = implode(".", array_slice($nameArr, 0, -1));
        }
        // Remove group name if not parent 
        else if (!$this->isParent()) {
            $tabName = '';
        }
        if ($tabName) {
            $tabName .= '.';
        }
        $tabName .= $tab['name'];
        $tab['key'] = $tab['name'];
        $tab['name'] = $tabName;
        // Make column
        $columnName = $this->getTabColumnName($tab);
        $columnType = $this->getTabColumnType($tab);
        $tab['column'] = [
            'name' => $columnName,
            'type' => $columnType,
        ];
        // Make fields
        $tabFields = [];
        foreach ($tab['fields'] as $name => $field) {
            if (is_numeric($name)) {
                $name = $field['name'];
            }
            $name = $tabName . '.' . $name;
            $element = $this->elementFactory->make(
                $field,
                $name,
                $this,
                $this->getProperties(),
                $this->configPath
            );
            $tabFields[] = $element;
        }
        $tab['fields'] = $tabFields;
        // Make wrapper
        $tab['itemWrapper'] = $this->getItemWrapper($tab);
        $tab['panelWrapper'] = $this->getItemWrapper($tab, 'panelWrapper');

        return $tab;
    }

    /**
     * Get the element table column name
     * 
     * @param array $tab
     * @return string
     */
    protected function getTabColumnName($tab): string
    {
        $column = $tab['column'] ?? [];

        if (is_array($column)) {
            $column =  $column['name'] ?? '';
        }

        if (empty($column)) {
            return $tab['name'];
        }

        // Replace with column name if column name is set
        $key = $tab['name'];

        if (strpos($key, ".") !== false) {
            $keyArr = explode('.', $key);
            $keyArr[count($keyArr) - 1] = $column;
            return implode('.', $keyArr);
        }

        return $column;
    }

    /**
     * Get the element table column type
     * 
     * @param array $tab
     * @return string
     */
    protected function getTabColumnType($tab): string
    {
        $column = $tab['column'] ?? [];

        if (is_array($column)) {
            return $column['type'] ?? '';
        }

        return '';
    }

    /**
     * Get Item wrapper
     *
     * @param array $tab
     * @param string $key
     * @return array
     */
    protected function getItemWrapper($tab, $key = 'itemWrapper'): array
    {
        // Replace the data
        $replaceData = $this->getConfigByKey('replace') ?? [];
        $replaceData['{name}'] = $this->getName();
        $replaceData['{id}'] = $this->getId();
        $replaceData['{type}'] = $this->getType();
        $replaceData['{tabName}'] = $tab['name'];
        $replaceData['{tabKey}'] = $tab['key'];

        // Wrapper Builder
        $wBuilder = clone $this->wrapperBuilder;
        $wBuilder->replace($replaceData);

        $wrapper = $tab[$key] ?? [];

        if (empty($wrapper)) {
            $wrapper = $this->getConfigByKey($key) ?? [];
        }
        $actvieClass = $wrapper['activeClass'] ?? 'active';

        // Check is active tab
        $wBuilder->replace('{activeClass}', '');
        if ($tab['key'] == $this->getActiveTab()) {
            $wBuilder->replace('{activeClass}', $actvieClass);
        }
        // Add Error class to element
        $errorClass = $wrapper['errorClass'] ?? '';
        $errorClass = str_replace(array_keys($replaceData), array_values($replaceData), $errorClass);
        $wBuilder->replace('{errorClass}', '');
        if (isset($tab['invalid']) && $tab['invalid']) {
            $wBuilder->replace('{errorClass}', $errorClass);
        }

        $tabWrapper = $wBuilder
            ->set($wrapper)
            ->build();

        return ['wrapper' => $tabWrapper, 'activeClass' => $actvieClass, 'errorClass' => $errorClass];
    }

    /**
     * Get Active tab
     * 
     * @return string
     */
    public function getActiveTab()
    {
        return $this->field['active'] ?? $this->tabs[0]['key'];
    }

    /**
     * Get tabs
     * 
     * @return Zk\FormBuilder\Contracts\Element[]
     */
    public function getTabs()
    {
        // Set invalid tab and Make wrapper
        foreach ($this->tabs as $key => $tab) {
            // Set invalid tab
            $tab['invalid'] = $this->isInvalidTab($tab);
            $this->tabs[$key] = $tab;
        }

        return $this->tabs;
    }

    /**
     * Modify element data before element set data
     * 
     * @param mixed $data
     * @return mixed 
     */
    public function modifyData($data): mixed
    {
        $modifyData = $data;

        foreach ($this->tabs as $tab) {
            foreach ($tab['fields'] as $field) {
                $modifyData = $field->modifyData($modifyData);
            }
        }

        return $modifyData;
    }

    /**
     * Set element data
     * 
     * @return void
     */
    public function setData($data): void
    {
        $this->data = $data;

        foreach ($this->tabs as $tab) {
            foreach ($tab['fields'] as $field) {
                $field->setData($this->data);
            }
        }
    }

    /**
     * Determine if element has rules
     *
     * @param string $side
     * @return bool
     */
    public function hasRules(string $side = 'backend'): bool
    {
        foreach ($this->tabs as $tab) {
            foreach ($tab['fields'] as $field) {
                if ($field->hasRules($side)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validate element
     *
     * @param string $prefix
     * @return void
     */
    public function validate(array $messages = [])
    {
        if (!$this->shouldValidate()) {
            return;
        }

        // Validate fields
        foreach ($this->tabs as $tab) {
            foreach ($tab['fields'] as $field) {
                $field->validate($messages);
            }
        }
    }

    /**
     * Check is invalid
     *
     * @return bool
     */
    public function isInvalid(): bool
    {
        if ($this->validator === null) {
            return false;
        }

        foreach ($this->tabs as $tab) {
            foreach ($tab['fields'] as $field) {
                if ($field->isInvalid()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check is invalid tab
     *
     * @return bool
     */
    public function isInvalidTab($tab)
    {
        foreach ($tab['fields'] as $field) {
            if ($field->isInvalid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get tabs to array
     * 
     * @param string $side frontend|backend
     * @return array
     */
    protected function getTabsToArray($side = 'frontend')
    {
        $tabs = [];
        foreach ($this->getTabs() as $key => $tab) {
            foreach ($tab['fields'] as $fKey => $field) {
                $tab['fields'][$fKey] = $field->toArray($side);
            }
            $tabs[$key] = $tab;
        }

        return $tabs;
    }

    /** 
     * Check is parent
     * 
     * @return bool
     */
    public function isParent(): bool
    {
        return $this->field['parent'] ?? false;
    }

    /**
     * Get element`s attributes
     * 
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = parent::getAttributes();

        // Remove 'placeholder' attribute
        unset($attributes['placeholder']);

        return $attributes;
    }

    /**
     * Load element data from entity
     *
     * @param Illuminate\Database\Eloquent\Model $entity
     * @return array
     */
    public function load($entity): array
    {
        if ($entity === null || !$this->isPersist()) {
            return [];
        }

        // Get entity attributes and convert to array format 
        $attributes = $entity->getAttributes();
        $newAttributes = Arr::undot($attributes);

        // If element is parent, add child data to parent 
        if ($this->isParent()) {
            // Add child data to parent
            $childData = Arr::get($newAttributes, $this->getColumnName(), []);
            Arr::forget($newAttributes, $this->getColumnName());
            // Format data to save as per column type
            if ($this->getColumnType() === 'json' && !empty($childData)) {
                $childData = json_decode($childData, true);
            }
            // Set child data to parent
            Arr::set($newAttributes, $this->getNameKey(), $childData);
        }

        // If element is parent, add child data to parent 
        foreach ($this->tabs as $tab) {
            $tabChildData = Arr::get($newAttributes, $tab['column']['name'], []);
            Arr::forget($newAttributes, $tab['column']['name']);
            // Format data to save as per column type
            if ($tab['column']['type'] === 'json' && !empty($tabChildData)) {
                $tabChildData = json_decode($tabChildData, true);
            }
            // Set child data to parent
            Arr::set($newAttributes, $tab['name'], $tabChildData);
        }

        // Set entity attributes
        $entity->setRawAttributes($newAttributes);

        // Load fields
        $childData = [];
        foreach ($this->tabs as $tab) {
            foreach ($tab['fields'] as $field) {
                $childData = array_merge($childData, $field->load($entity));
            }
        }

        return $childData;
    }

    /**
     * Fill element data to entity
     *
     * @param Illuminate\Database\Eloquent\Model $entity
     * @param array $data
     * @param bool $emptyOnNull
     * @return void
     */
    public function fill($entity, $data, $emptyOnNull = true)
    {
        if (!$this->isPersist()) {
            return;
        }

        // Fill fields
        foreach ($this->tabs as $tab) {
            foreach ($tab['fields'] as $field) {
                $field->fill($entity, $data, $emptyOnNull);
            }
        }

        // Get entity attributes and convert to array format 
        $attributes = $entity->getAttributes();
        $newAttributes = Arr::undot($attributes);

        // If element is parent, add child data to parent 
        foreach ($this->tabs as $tab) {
            $tabChildData = Arr::get($newAttributes, $tab['name'], []);
            Arr::forget($newAttributes, $tab['name']);
            // Format data to save as per column type
            if ($tab['column']['type'] === 'json' && !empty($tabChildData)) {
                $tabChildData = json_encode($tabChildData);
            }
            // Set child data to parent
            Arr::set($newAttributes, $tab['column']['name'], $tabChildData);
        }

        if ($this->isParent()) {
            // Add child data to parent
            $childData = Arr::get($newAttributes, $this->getNameKey(), []);
            Arr::forget($newAttributes, $this->getNameKey());
            // Format data to save as per column type
            if ($this->getColumnType() === 'json' && !empty($childData)) {
                $childData = json_encode($childData);
            }
            // Set child data to parent
            Arr::set($newAttributes, $this->getColumnName(), $childData);
        }

        // Set entity attributes
        $entity->setRawAttributes($newAttributes);
    }

    /**
     * Transforms element to array
     * 
     * @param string $side frontend|backend
     * @return array
     */
    public function toArray($side = 'frontend'): array
    {
        return [
            'activeTab' => $this->getActiveTab(),
            'parent' => $this->isParent(),
            'type' => $this->getType(),
            'name' => $this->getName(),
            'id' => $this->getId(),
            'nameKey' => $this->getNameKey(),
            'key' => $this->getKey(),
            'label' => $this->getLabel() ? $this->getLabel()->toArray() : false,
            'before' => $this->getBefore(),
            'after' => $this->getAfter(),
            'wrapper' => $this->getWrapper(),
            'tabWrapper' => $this->getWrapper('tabWrapper'),
            'contentWrapper' => $this->getWrapper('contentWrapper'),
            'attributes' => $this->getAttributes(),
            // 'value' => $this->getValue(),
            'tabs' => $this->getTabsToArray($side),
            'invalid' => $this->isInvalid(),
            'component' => $this->getComponent(),
            'jsElement' => $this->getJsElement(),
            'elementType' => $this->getElementType(),
            'viewOnly' => $this->hasViewOnly(),
        ];
    }
}
