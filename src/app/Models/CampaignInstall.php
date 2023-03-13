<?php

namespace App\Models;


/**
 * @property int       $id
 * @property int       $campaign_id
 * @property int       $partner_campaign_id
 * @property int       $partner_id
 * @property \DateTime $installed_at
 * @property \DateTime $date_install
 * @property string    $device_id
 * @property string    $ip
 * @property string    $os
 * @property string    $price
 * @property \DateTime $faked_at
 */
class CampaignInstall extends BaseModel
{
    protected $table = 'campaign_installs';
    public $timestamps = false;
    protected $fillable = [
    'campaign_id',
    'partner_campaign_id',
    'partner_id',
    'installed_at',
    'device_id',
    'ip',
    'os',
    'faked_at'
];
}
