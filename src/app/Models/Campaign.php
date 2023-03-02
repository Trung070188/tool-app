<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $package_id
 * @property string $icon
 * @property int $price
 * @property string $os
 * @property int $customer_id
 * @property int $daily_fake_install
 * @property string $store_url
 * @property int $open_next_day
 * @property int $is_fake_on
 * @property \DateTime $auto_off_at
 * @property \DateTime $auto_on_at
 * @property string $note
 * @property int $total_install
 * @property string $type
 * @property \DateTime $created_at
 * @property \DateTime $deleted_at
 * @property \DateTime $updated_at
 * @property int $status
 */
class Campaign extends BaseModel
{
    use SoftDeletes;

    protected $table = 'campaigns';
    protected $fillable = [
        'name',
        'package_id',
        'icon',
        'price',
        'os',
        'customer_id',
        'type',
        'status',
        'open_next_day',
        'daily_fake_install',
        'store_url',
        'is_fake_on',
        'note',
        'total_install',
        'auto_on_at',
        'auto_off_at'
    ];

    protected $casts = [
        'icon' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function campaignPartner()
    {
        return $this->hasMany(CampaignPartner::class, 'campaign_id');
    }
}
