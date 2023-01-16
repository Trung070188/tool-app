<?php

namespace App\Models;


 /**
 * @property int       $id
 * @property string    $module
 * @property int       $parent_id
 * @property string    $name
 * @property string    $display_name
 * @property string    $class
 * @property string    $action
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property string    $note
 */
class Permission extends BaseModel
{
    protected $table = 'permissions';
    protected $fillable = [
    'module',
    'parent_id',
    'name',
    'display_name',
    'class',
    'action',
    'note',
];
}
