<?php

namespace App\Models;


 /**
 * @property int       $id
 * @property int       $campaign_id
 * @property int       $partner_campaign_id
 * @property int       $partner_id
 * @property \DateTime $installed_at
 * @property string    $device_id
 * @property string    $ip
 * @property string    $os
 */
class CampaignInstall extends BaseModel
{
    protected $table = 'campaign_installs';
    protected $fillable = [
    'campaign_id',
    'partner_campaign_id',
    'partner_id',
    'installed_at',
    'device_id',
    'ip',
    'os',
];
}
