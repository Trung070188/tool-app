<?php

namespace App\Models;


 /**
 * @property int       $id
 * @property string    $name
 * @property string    $email
 * @property string    $phone
 * @property string    $company
 * @property string    $description
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class Customer extends BaseModel
{
    protected $table = 'customers';
    protected $fillable = [
    'name',
    'email',
    'phone',
    'company',
    'description',
];
}
