<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;



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
class Customer extends Authenticatable
{
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'password',
        'description',
    ];
}