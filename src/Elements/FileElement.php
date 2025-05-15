<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Elements;

use Zk\FormBuilder\Helpers\WrapperBuilder;
use Zk\FormBuilder\Contracts\Element as ElementContract;
use Zk\FormBuilder\Contracts\Form;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileElement extends Element
{
    /**
     * Blade view component path.
     *
     * @var string
     */
    public $component = 'formbuilder::template.file';

    /**
     * Element type identifier.
     *
     * @var string
     */
    public $elementType = 'file';

    /**
     * JavaScript handler/component name.
     *
     * @var string
     */
    public $jsElement = 'ZkFileElement';

    /**
     * Upload configuration options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Custom upload handler (callable or string).
     *
     * @var mixed
     */
    protected $uploadFunc;

    /**
     * Custom fetch handler (callable or string).
     *
     * @var mixed
     */
    protected $fetchFunc;

    /**
     * Custom display handler (callable or string).
     *
     * @var mixed
     */
    protected $displayFunc;

    /**
     * Custom delete handler (callable or string).
     *
     * @var mixed
     */
    protected $deleteFunc;

    /**
     * Element constructor.
     *
     * Initializes the base state of the element, including its field data,
     * parent reference, configuration path, and injected dependencies.
     * Heavy logic like dynamic field setup should be handled in `init()`,
     * which must be called explicitly after successful instantiation.
     *
     * @param array $field Raw field definition, including name, type, label, etc.
     * @param Form $form
     * @param ElementContract | null $parent The parent element or null containing this element.
     * @param WrapperBuilder $wrapperBuilder Helper for rendering element wrappers.
     */
    public function __construct(
        array $field,
        protected Form $form,
        protected ElementContract | null $parent,
        protected WrapperBuilder $wrapperBuilder
    ) {
        parent::__construct($field, $form, $parent, $wrapperBuilder);
    }

    /**
     * Initialize the element's dynamic properties.
     *
     * This method should be called after the element is successfully constructed,
     * allowing for conditional rendering and dependency resolution before setup.
     *
     * @return void
     */
    public function init(): void
    {
        parent::init();

        $this->setOptions();

        $this->uploadFunc  = $field['upload']  ?? $this->getConfig('upload');
        $this->fetchFunc   = $field['fetch']   ?? $this->getConfig('fetch');
        $this->displayFunc = $field['display'] ?? $this->getConfig('display');
        $this->deleteFunc  = $field['delete']  ?? $this->getConfig('delete');

        // Ensure the parent form is set to support file uploads
        $this->getForm()->setHasFiles(true);
    }

    /**
     * Initialize file upload options with fallbacks.
     *
     * @return void
     */
    protected function setOptions(): void
    {
        $options = $this->field['options'] ?? $this->getConfig('options') ?? [];

        $this->options = [
            'delete'    => $options['delete']    ?? false,
            'disk'      => $options['disk']      ?? 'public',
            'path'      => $options['path']      ?? 'documents',
            'prefix'    => $options['prefix']    ?? '',
            'suffix'    => $options['suffix']    ?? '',
            'filename'  => $options['filename']  ?? 'original',
            'extension' => $options['extension'] ?? 'original',
        ];
    }

    /**
     * Retrieve upload options.
     *
     * @param string|null $key Specific option key, or all options if null.
     * @return mixed
     */
    public function getOptions($key = null)
    {
        return $key === null
            ? $this->options
            : ($this->options[$key] ?? '');
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
            $attributes['multiple'] = 'true';
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
            foreach ((array) $oldFiles as $file) {
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
                foreach ((array) $files as $file) {
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
     * Get files from storage
     *
     * @return array
     */
    public function getFiles(): array
    {
        $files = $this->getValue();

        // Use custom display function if set
        if (!empty($this->displayFunc)) {
            return call_user_func($this->displayFunc, $this, $files);
        }

        if (empty($files)) {
            return [];
        }

        $storage = Storage::disk($this->getOptions('disk'));
        $path = rtrim($this->getOptions('path'), '/') . '/';

        if ($this->isMultiple()) {
            return collect((array) $files)
                ->filter(fn($file) => !$file instanceof UploadedFile)
                ->map(fn($file) => $storage->url($path . $file))
                ->all();
        }

        if (!$files instanceof UploadedFile) {
            return [$storage->url($path . $files)];
        }

        return [];
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
        // $arr['options'] = $this->getOptions();

        return $arr;
    }
}
