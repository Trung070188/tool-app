<?php

function apiCampaignHistory(array $params,array $query = []) {
    $partnerRes = apiGetConnectedPartner($params);

    $requiredParams = [
        'partner_id',
        'partner_secret',
        'partner_campaign_id',
        'start',
        'end',
    ];


    foreach ($requiredParams as $key) {
        if (empty($params[$key])) {
            return [
                'code' => 1,
                'message' => 'Missing params: '  . $key
            ];
        }
    }

    if ($partnerRes['code'] !== 0) {
        return $partnerRes;
    }

    $partnerCampaignId = $params['partner_campaign_id'];
    $partner = $partnerRes['partner'];
    $startDate = $params['start'];
    $endDate = $params['end'];
    $db = apiGetDb();

    $partnerCampaign = $db->selectOne('SELECT * FROM partner_campaigns
         WHERE id=? AND partner_id=?', [
        $partnerCampaignId, $partner->id
    ]);


    if (!$partnerCampaign) {
        return [
            'code' => 3,
            'message' => 'Invalid campaign partner'
        ];
    }

    $startTime = strtotime($startDate);
    $endTime = strtotime($endDate);

    if ($startTime === false) {
        return [
            'code' => 8,
            'message' => 'Thời gian bắt đầu không hợp lệ'
        ];
    }
    if ($endTime === false) {
        return [
            'code' => 8,
            'message' => 'Thời gian kết thúc không hợp lệ'
        ];
    }


    $span = intval(($endTime - $startTime)/86400);
    $startDate = date('Y-m-d', $startTime);
    $endDate = date('Y-m-d', $endTime);


    if ($startTime > $endTime) {
        return [
            'code' => 7,
            'message' => 'Thời gian không hợp lệ'
        ];
    }

    if ($span > 30) {
        return [
            'code' => 6,
            'message' => 'Thời gian thống kê tối đa 30 ngày'
        ];
    }

    $installHistory = $db->select('SELECT date_install `date`, COUNT(*) num_install FROM campaign_installs
WHERE faked_at is null AND partner_id=? AND partner_campaign_id=? AND date_install >=? AND date_install <=?
GROUP BY `date_install`
', [
        $partner->id, $partnerCampaignId, $startDate, $endDate
    ]);

    return [
        'code' => 0,
        'data' => [
            'campaign' => $partnerCampaignId,
            'span' => $span,
            'installs' => $installHistory
        ]
    ];
}
