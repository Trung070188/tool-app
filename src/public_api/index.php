<?php
require '../lib/api_functions.php';
require '../lib/ApiDB.php';

function main(): array
{
    list($requestUri) = explode('?', $_SERVER['REQUEST_URI']);
    $routeMap = [
        '/api/campaigns/install' => 'apiCampaignInstall'
    ];


    if (!isset($routeMap[$requestUri])) {
        http_response_code(404);
        return [
            'code' => 404,
            'message' => 'Page not found'
        ];
    }

    $query = $_GET;
    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        $params = $_GET;
    } else {
        $HTTP_CONTENT_TYPE = $_SERVER['HTTP_CONTENT_TYPE'] ?? null;
        if ($HTTP_CONTENT_TYPE === 'application/json') {
            $inputJSON = file_get_contents('php://input');
            $params = json_decode($inputJSON, TRUE);
        } else {
            $params = $_POST;
        }
    }
    $callback = $routeMap[$requestUri];


    return $callback($params ?? [], $query ?? []);
}

/**
 * @api {POST} https://api.seogameapp.com/api/campaigns/install Save a valid install
 * @apiName SaveValidInstall
 * @apiGroup API
 * @apiDescription Mã lỗi 0 là thành công, khác 0 là thất bại. Các mã lỗi thất bại là 1,2,3,4,5 do thiếu param hoặc sai key
 * @apiParam {String} partner_id (required)
 * @apiParam {String} partner_secret (required)
 * @apiParam {String} partner_campaign_id (required)
 * @apiParam {String} package_id (required)
 * @apiParam {String} ip (required) User's IP
 * @apiParam {String} os (required) android|ios
 * @apiPayloadExample
 *  {
"partner_id": 2,
"partner_secret": "e4je7eJEgj5ZOzeWEsAiz2qg7wp9nKgt",
"partner_campaign_id": 2,
"device_id": "abc",
"package_id": "com.b52tg445aw.whitestarworld",
"ip": "::1",
"os": "android"
}
 *
 * @apiSuccessExample Success-Response:
{
"code": 1,
"message": "OK"
}
 * @apiSuccessExample Error-Response:
{
"code": 1,
"message": "Missing request params"
}


 */
function apiCampaignInstall(array $params,array $query = []) {
    $requiredParams = [
        'partner_id',
        'partner_secret',
        'partner_campaign_id',
        'ip',
        'device_id',
        'os'
    ];


    foreach ($requiredParams as $key) {
        if (empty($params[$key])) {
            return [
                'code' => 1,
                'message' => 'Missing params: '  . $key
            ];
        }
    }



    $os = $params['os'];
    if ($os !== 'android' && $os !== 'ios') {
        return [
            'code' => 1,
            'message' => "Os must be ios | android"
        ];
    }

    $partnerId = $params['partner_id'];
    $partnerSecret = $params['partner_secret'];
    $partnerCampaignId = $params['partner_campaign_id'];
    $packageId = $params['package_id'];
    $deviceId = $params['device_id'];
    $ip = $params['ip'];

    $db = apiGetDb();
    $partner = $db->selectOne("SELECT * FROM partners WHERE id=?", [
        $partnerId
    ]);

    if (!$partner) {
        return [
            'code' => 2,
            'message' => 'Invalid partner'
        ];
    }

    if (!hash_equals($partner->secret, $partnerSecret)) {
        return [
            'code' => 21,
            'message' => 'Invalid partner secret'
        ];
    }

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


    if (intval($partnerCampaign->status) == 0) {
        return [
            'code' => 5,
            'message' => "Campaign partner `{$partnerCampaign->id}-{$partnerCampaign->name}` is OFF"
        ];
    }

    $campaign = $db->selectOne('SELECT * FROM campaigns  WHERE id=?', [$partnerCampaign->campaign_id]);

    if (!$campaign) {
        return [
            'code' => 4,
            'message' => 'Invalid campaign'
        ];
    }

    if (intval($campaign->status) == 0) {
        return [
            'code' => 5,
            'message' => "Campaign `{$campaign->id}-{$campaign->name}` is OFF"
        ];
    }

    if ($campaign->package_id !== $packageId) {
        return [
            'code' => 9,
            'message' => "Campaign packageId `{$campaign->id}-{$campaign->name}-{$campaign->package_id}` not matched "
        ];
    }

    $exists = $db->selectOne('SELECT * FROM campaign_installs WHERE campaign_id=? AND device_id=?', [
        $campaign->id, $deviceId
    ]);

    if ($exists) {
        return [
            'code' => 6,
            'message' => "Device `$deviceId` already exists"
        ];
    }

    $now = date('Y-m-d H:i:s');
    $date = date('Y-m-d');

    $db->insert('campaign_installs', [
        'campaign_id' => $campaign->id,
        'partner_campaign_id' => $partnerCampaignId,
        'partner_id' => $partner->id,
        'installed_at' => $now,
        'date_install' => $date,
        'device_id' => $deviceId,
        'ip' => $ip,
        'price' => $partnerCampaign->price,
        'package_id' => $packageId,
        'os' => $os
    ]);

    return [
        'code' => 0,
        'message' => 'OK'
    ];
}

$exception = null;
$httpCode = 200;

try {
    $response = main();
} catch (\Exception $ex) {
    $exception = $ex;
    apiWriteLog($ex);
    $httpCode = 503;
    http_response_code(503);

    $response = [
        'code' => 503,
        'message' => 'Internal server error'
    ];
}

apiLogResponse($response, $exception, $httpCode);

header('Content-Type: application/json');
echo json_encode($response);
