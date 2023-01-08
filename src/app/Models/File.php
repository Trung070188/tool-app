<?php

namespace App\Models;


 /**
 * @property string    $id
 * @property string    $parent_id
 * @property int       $is_folder
 * @property string    $name
 * @property string    $path
 * @property string    $hash
 * @property string    $url
 * @property int       $size
 * @property string    $type
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property string    $uploaded_by
 * @property int       $is_image
 * @property string    $extension
 * @property int       $user_id
 * @property \DateTime $deleted_at
 * @property string    $deleted_by
 */
class File extends BaseModel
{
    protected $table = 'files';
    protected $fillable = [
    'parent_id',
    'is_folder',
    'name',
    'path',
    'hash',
    'url',
    'size',
    'type',
    'uploaded_by',
    'is_image',
    'extension',
    'user_id',
    'deleted_by',
];
}
