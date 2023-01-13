<?php

namespace App\Models;


 /**
 * @property int       $ partner_campaign_id
 * @property int       $campaign_id
 * @property int       $partner_id
 */
class CampaignPartner extends BaseModel
{
    protected $table = 'partner_campaigns';
    protected $fillable = [
    'id',
    'name',
    'campaign_id',
    'partner_id',
    'price',
    'os',
    'url_partner',
    'open_next_day',
    'status',
    'note'
];
}
