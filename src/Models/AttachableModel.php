<?php namespace Williamoliveira\Attachable\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Williamoliveira\Attachable\Contracts\AttachableHelpersContract;
use Williamoliveira\Attachable\Contracts\AttachableModelContract;
use Williamoliveira\Attachable\Contracts\InterpolatorContract;
use Williamoliveira\Attachable\Contracts\StorageContract;
use Williamoliveira\Attachable\Exceptions\OnlyImagesException;
use Williamoliveira\Attachable\Observers\AttachableModelObserver;
use Williamoliveira\Attachable\Services\Interpolator;
use Williamoliveira\Attachable\Services\RemoteFileManager;

/**
 * Class AttachableModel
 * @package Williamoliveira\Attachable\Models
 */
class AttachableModel extends Model implements AttachableModelContract
{

    public $timestamps = true;

    public $onlyImages = false;

    public $filesPath = '';

    public $imagesPath = '';

    protected $realModelName = null;

    protected $table = 'attachable_files';

    protected $fillable = [
        'file',
        'file_name',
        'file_extension',
        'mime_type',
        'file_size',
        'use_intervention_image'
    ];

    protected $appends = ['path', 'url'];

    protected $casts = [
        'use_intervention_image' => 'boolean',
        'file_size' => 'integer'
    ];

    /**
     * @var File
     */
    protected $file;

    protected $defaultTemplate = 'original';

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['model'] = get_class($this);
    }

    protected static function boot()
    {
        parent::boot();
        static::observe(AttachableModelObserver::class);
        static::addGlobalScope(new ModelScope());
    }
    
    
    /**
     * @return array
     */
    public function imageTemplates()
    {
        return [
            'original' => function (Image $image){
                return $image;
            }
        ];
    }

    /**
     * @param $file
     * @throws Exception
     */
    public function setFileAttribute($file)
    {
        $this->setFile($file);
    }

    /**
     * @param string $templateName
     * @return mixed
     */
    public function getContents($templateName = 'original')
    {
        $path = app('attachable.interpolator')->parsePath($this, $templateName);

        return app('attachable.storage')->setDisk($this->disk)->get($path);
    }

    /**
     * @param $value
     */
    public function setFilenameAttribute($value)
    {
        if(empty($value)){
            return;
        }

        $this->attributes['file_name'] = app('attachable.helpers')->sanitizeFilename($value);
    }

    /**
     * @param $file
     * @return AttachableModel
     * @throws Exception
     */
    public function setFile($file)
    {
        if($file instanceof UploadedFile){
            return $this->setupFileAttributes($file);
        }

        if(filter_var($file, FILTER_VALIDATE_URL)){
            $file = app(RemoteFileManager::class)->get($file);

            return $this->setupFileAttributes($file);
        }

        throw new \InvalidArgumentException('Not a ' . UploadedFile::class. ' instance neither a URL');
    }

    /**
     * @return mixed
     */
    public function getPathAttribute()
    {
        return $this->getPath('original');
    }

    /**
     * @return string
     */
    public function getBasenameAttribute()
    {
        return $this->attributes['file_name'] . '.' . $this->attributes['file_extension'];
    }

    /**
     * @param string $templateName
     * @return mixed
     */
    public function getPath($templateName = 'original')
    {
        return app(Interpolator::class)->parsePath($this, $templateName);
    }

    /**
     * @return mixed|string
     */
    public function getUnparsedImagesPath()
    {
        return !empty($this->imagesPath) ? $this->imagesPath : config('attachable.images_fallback_path');
    }

    /**
     * @return mixed|string
     */
    public function getUnparsedFilesPath()
    {
        return !empty($this->filesPath) ? $this->filesPath : config('attachable.files_fallback_path');
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function useInterventionImage()
    {
        $imagesPathHasTemplate = app(InterpolatorContract::class)
            ->pathHas($this->getUnparsedImagesPath(), 'template');
        $fileIsImage = app(AttachableHelpersContract::class)->isImage($this->file);

        return ($imagesPathHasTemplate && $fileIsImage);
    }

    /**
     * @param null $templateName
     * @return mixed
     */
    public function url($templateName = null)
    {
        $templateName = $templateName ? $templateName : $this->defaultTemplate;
        $filepath = $this->getPath($templateName);

        return app(StorageContract::class)->setDisk($this->getDisk())->getUrl($filepath);
    }

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->url();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * @return string
     */
    public function getRealModelName()
    {
        return $this->realModelName ?: static::class;
    }

    /**
     * @param UploadedFile $file
     * @return $this
     * @throws Exception
     */
    private function setupFileAttributes(UploadedFile $file)
    {
        $this->checkOnlyImageRestriction($file);

        $this->file = $file;
        $this->file_extension = $file->getClientOriginalExtension() ?: $file->guessExtension();
        $this->file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $this->mime_type = $file->getMimeType();
        $this->file_size = $file->getSize();
        $this->use_intervention_image = $this->useInterventionImage();

        return $this;
    }

    /**
     * @param UploadedFile $file
     * @throws OnlyImagesException
     */
    private function checkOnlyImageRestriction(UploadedFile $file)
    {
        if($this->onlyImages && !app(AttachableHelpersContract::class)->isImage($file)){
            throw new OnlyImagesException();
        }
    }

    /**
     * @return string
     */
    public function getDisk()
    {
        return $this->disk ?: config('attachable.default_disk');
    }
}
