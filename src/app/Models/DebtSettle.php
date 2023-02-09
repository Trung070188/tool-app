<?php

namespace App\Models;


 /**
 * @property int       $customer_id
 * @property string    $pay_booking
 * @property string    $pay_debt
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime $deleted_at
 */
class DebtSettle extends BaseModel
{
    protected $table = 'debt_settle';
    protected $fillable = [
    'id',
    'customer_id',
    'pay_booking',
    'pay_debt',
];
}
