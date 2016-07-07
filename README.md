# Attachable
Attach files to Laravel Eloquent models, works with any Laravel filesystem driver


# Instalation

```
composer require williamoliveira/attachable@dev-master
php artisan vendor:publish
php artisan migrate
```


# Usage

Extend `Williamoliveira\Attachable\Models\AttachableModel` and configure it as you need

```php
<?php

namespace App\Models;

use App\Services\TenantContext;
use Intervention\Image\Image as InterventionImage;
use Williamoliveira\Attachable\Models\AttachableModel;

class Image extends AttachableModel
{
    // Set true if you want the model to accepts only images
    public $onlyImages = true;

    // Define the path where images will be stored, you can use id, filename, template and extension wildcards
    // (Fallsback to 'images_fallback_path' in your config file)
    public $imagesPath = 'images/{id}/{filename}--{template}.{extension}';
    
    // Define the path where files will be stored, you can use id, filename and extension wildcards
    // (Only for AttachableModel) 
    public $filesPath = 'files/{id}/{filename}.{extension}';
    
    // Define the default image template
    // (Optional, defaults to 'original') 
    protected $defaultTemplate = 'normal';
    
    // Define the Laravel Filesystem disk wich you be used to store the files
    // (Optional, fallsback to 'default_disk' on config file)
    public $disk = 'local_public';

    // Define your image modification, you can use anything from the Image Intervetion API
    // These image templates will be stored to disk, kinda like what Wordpress does, if you are familiar
    public function imageTemplates()
    {
        return [
            'original' => function (InterventionImage $image) {
                return $image;
            },
            'normal' => function (InterventionImage $image) {
                return $image->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            },
            'thumbnail' => function (InterventionImage $image) {
                return $image->sharpen(10)->fit(200, 150);
            }
        ];
    }
}
```

As its just a normal Eloquent model you can attach it to your others eloquent models like so:

```php
//...
    public function image()
    {
        return $this->morphOne(\App\Models\Image::class, 'attachable');
    }
//...
```

An AttachableModel has the following attributes:
```
'file', // An URL string or an instance of Symfony\Component\HttpFoundation\File\UploadedFile
'file_name',
'file_extension',
'mime_type',
'file_size'
```

Must of the time all you need to set is the file attribute, the others are set automatizaly based on the file

You can instantiate an AttachableModel in any Eloquent way, like so:
```php
$image = Image::create(['file' => $myUploadedFile]);
```

You can get the public URL of the image using `$image->url($imageTemplateName)`,
but right now only works for S3 driver or local public on a disk name hardcoded to 'local_public' (yeah, yeah, I will improve that later)

# Tips

If you want to store your images to public folder, create a new disk on config/filesystems.php, like so:
```php
'local_public' => [
  'driver' => 'local',
  'root'   => public_path('storage'),
],
```
than change config/attachable.php 'default_disk' attribute to 'local_public'

