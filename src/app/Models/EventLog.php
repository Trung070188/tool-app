<?php

namespace App\Models;
/**
 * @property int       $id
 * @property string    $title
 * @property string    $content
 * @property \DateTime $time
 * @property int       $campaign_id
 */
class EventLog extends BaseModel
{
    public $timestamps = false;
    protected $table = 'event_logs';
}
