<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Zk\FormBuilder\Helpers\WrapperBuilder;
use Illuminate\Support\Str;

class FileElement extends Element
{
    /**
     * Component's name
     * 
     * @var string
     */
    public $component = 'formbuilder::template.file';

    /**
     * Element type
     *
     * @var string
     */
    public $elementType = 'file';

    /**
     * Javascript Element
     * 
     * @var string
     */
    public $jsElement = 'ZkFileElement';

    /**
     * Element upload options
     * 
     * @var array
     */
    protected $options = [];

    /**
     * Element custom upload function
     * 
     * @var mixed
     */
    protected $uploadFunc;

    /**
     * Element custom load function
     * 
     * @var mixed
     */
    protected $fetchFunc;

    /**
     * Element custom files function
     * 
     * @var mixed
     */
    protected $displayFunc;

    /**
     * Element custom delete function
     * 
     * @var mixed
     */
    protected $deleteFunc;

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

        $this->setOptions();

        $this->uploadFunc = $this->field['upload'] ?? $this->getConfigByKey('upload');
        $this->fetchFunc = $this->field['fetch'] ?? $this->getConfigByKey('fetch');
        $this->displayFunc = $this->field['display'] ?? $this->getConfigByKey('display');
        $this->deleteFunc = $this->field['delete'] ?? $this->getConfigByKey('delete');

        // Form has files so set enctype to multipart/form-data
        $this->getForm()->files = true;
    }

    /**
     * Set upload options
     * 
     * @return void
     */
    protected function setOptions()
    {
        $upload = $this->field['options'] ?? $this->getConfigByKey('options');

        $this->options['delete'] = $upload['delete'] ?? false;
        $this->options['disk'] = $upload['disk'] ?? 'public';
        $this->options['path'] = $upload['path'] ?? 'documents';
        $this->options['prefix'] = $upload['prefix'] ?? '';
        $this->options['suffix'] = $upload['suffix'] ?? '';
        $this->options['filename'] = $upload['filename'] ?? 'original';
        $this->options['extension'] = $upload['extension'] ?? 'original';
    }

    /**
     * Get upload options
     * 
     * @param string $key
     * @return array | mixed
     */
    public function getOptions($key = null)
    {
        if ($key === null) {
            return $this->options;
        }

        return $this->options[$key] ?? '';
    }

    /**
     * Check if element is allow multiple files
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->field['multiple'] ?? false;
    }

    /**
     * Get the element name
     * 
     * @return string
     */
    public function getName(): string
    {
        $name = ($this->isMultiple()) ? $this->getNameKey() . '[]' : $this->getNameKey();

        return $this->toBracketNotation($name);
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

        // Check is multiple and add 'multiple' attribute
        if ($this->isMultiple()) {
            $attributes[] = 'multiple';
        }

        return $attributes;
    }

    /**
     * Convert Element rule to validation format
     * 
     * @param string $side
     * @return array
     */
    public function toValidationRules(string $side = 'backend'): array
    {
        $ruleKey = ($this->isMultiple()) ? $this->getNameKey() . '*' : $this->getNameKey();
        return [$ruleKey => $this->getRules($side)];
    }

    /**
     * Upload file to storage
     * 
     * @param Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function upload(\Illuminate\Http\UploadedFile $file)
    {
        $disk = $this->getOptions('disk'); // public, s3 etc.. default is public
        $path = $this->getOptions('path'); // if disk is s3 then path is s3 bucket path default is documents
        $prefix = $this->getOptions('prefix'); // prefix add before file name
        $suffix = $this->getOptions('suffix'); // suffix add after file name
        $filename = $this->getOptions('filename');  // 'original' or 'random' and 'custom' default is original if custom and original then add random string after file name
        $extension = $this->getOptions('extension'); // 'original' or 'custom' default is original if custom then add custom extension

        // Get storage disk
        $storage = Storage::disk($disk);
        // Get file name
        $fileName = $file->getClientOriginalName();
        // Get file extension
        $fileExtension = $file->getClientOriginalExtension();

        // Check if file name
        if ($filename === 'random') {
            $fileName = Str::random(10);
        } else if ($filename !== 'original') {
            $fileName = $filename . '-' . Str::random(5);
        } else {
            $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '-' . Str::random(5);
        }
        $fileName = $prefix . $fileName . $suffix;
        // Check if file extension type is custom
        if ($extension !== 'original') {
            $fileExtension = $extension;
        }
        // File name replace space with underscore
        $fileName = str_replace(' ', '_', $fileName);

        // File name with extension
        $fileName = $fileName . '.' . $fileExtension;

        // Store file
        $path = $storage->putFileAs($path, $file, $fileName);

        return [
            'filename' => $fileName,
            'path' => $path,
        ];
    }

    /**
     * Delete file from storage
     * 
     * @param Illuminate\Database\Eloquent\Model $entity
     * @return void
     */
    public function delete($entity)
    {
        if (!$this->getOptions('delete')) return;

        if (!empty($this->deleteFunc)) {
            $delete = $this->deleteFunc;
            $delete($this, $entity);
            return;
        }
        if (!$entity->exists || empty($entity[$this->getColumnName()])) return;

        // Get old files from entity and delete
        $oldFiles = $entity[$this->getColumnName()];
        if ($this->getColumnType() === 'json') {
            $oldFiles = json_decode($oldFiles, true);
        }
        $storage = Storage::disk($this->getOptions('disk'));
        $path = $this->getOptions('path') . '/';
        if ($this->isMultiple()) {
            foreach ($oldFiles as $file) {
                $storage->delete($path . $file);
            }
        } else {
            $storage->delete($path . $oldFiles);
        }
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

        $data = Arr::get($entity->toArray(), $this->getColumnName());

        // Check if custom fetch function
        if (!empty($this->fetchFunc)) {
            $fetch = $this->fetchFunc;
            $data = $fetch($this, $entity, $data);
            return [$this->getNameKey() => $data];
        }

        // Format data to save as per column type
        if ($this->getColumnType() === 'json' && !empty($data)) {
            $data = json_decode($data, true);
        }

        return [
            $this->getNameKey() => $data
        ];
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
        if (!$this->isPersist() || $this->hasViewOnly()) {
            return;
        }

        if (Arr::has($data, $this->getNameKey())) {
            $files = Arr::get($data, $this->getNameKey());
            // Check if custom upload function
            if (!empty($this->uploadFunc)) {
                $upload = $this->uploadFunc;
                $entity[$this->getColumnName()] = $upload($this, $entity, $files);
                return;
            }

            $uploadedFile = '';
            if ($this->isMultiple()) {
                $uploadedFiles = [];
                foreach ($files as $file) {
                    $uploadedFiles[] = $this->upload($file);
                }
                $uploadedFile = array_column($uploadedFiles, 'filename');
            } else {
                $uploadedFiles = $this->upload($files);
                $uploadedFile = $uploadedFiles['filename'];
            }

            // Delete old files
            $this->delete($entity);

            // Format data to save as per column type
            if ($this->getColumnType() === 'json' && !empty($uploadedFile)) {
                $uploadedFile = json_encode($uploadedFile);
            }
            $entity[$this->getColumnName()] = $uploadedFile;
        } else if ($emptyOnNull) {
            $this->empty($entity);
        }
    }

    /**
     * Get files
     * 
     * @return array
     */
    public function getFiles()
    {
        $files = $this->getValue();

        // Check if custom display function
        if (!empty($this->displayFunc)) {
            $display = $this->displayFunc;
            return $display($this, $files);
        }

        if (empty($files)) return [];
        $storage = Storage::disk($this->getOptions('disk'));
        $path = $this->getOptions('path') . '/';
        $filesPath = [];
        if ($this->isMultiple()) {
            foreach ($files as $file) {
                $filesPath[] = $storage->url($path . $file);
            }
        } else {
            $filesPath[] = $storage->url($path . $files);
        }
        return $filesPath;
    }

    /**
     * Transforms element to array
     * 
     * @param string $side frontend|backend
     * @return array
     */
    public function toArray($side = 'frontend'): array
    {
        $arr = parent::toArray($side);

        $arr['multiple'] = $this->isMultiple();
        $arr['files'] = $this->getFiles();

        return $arr;
    }
}
