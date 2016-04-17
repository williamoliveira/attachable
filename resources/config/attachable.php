<?php

return [

    'default_disk' => 'local',

    //Declare here the models being used (with full namespace)
    'models' => [
        Williamoliveira\Attachable\Models\AttachableModel::class,
    ],

    //Images fallback path, Placeholders: {id}, {template}, {filename}
    'images_fallback_path' => 'uploads/images/{id}/{template}/{filename}',

    //Files fallback path, Placeholders: {id}, {filename}
    'files_fallback_path' => 'uploads/files/{id}/{filename}'

];
