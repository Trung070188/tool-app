<?php

namespace App\Models;


 /**
 * @property int       $id
 * @property string    $name
 * @property string    $ip
 * @property string    $note
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class Partner extends BaseModel
{
    protected $table = 'partners';
    protected $fillable = [
        'name',
        'ip',
        'note',
        'secret',
        'check_copy'
];
}
