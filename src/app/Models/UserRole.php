<?php


namespace App\Models;


class UserRole extends BaseModel
{
    protected $table = 'user_roles';
    protected $fillable=[
        'id',
        'role_id',
        'user_id'
        ];

//    public function role() {
//        return $this->belongsTo(Role::class, 'role_id');
//    }
}
