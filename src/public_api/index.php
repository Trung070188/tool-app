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

function apiCampaignInstall(array $params,array $query = []) {
    $db = apiGetDb();
    $customers = $db->select("SELECT * FROM customers LIMIT 1");


    return $customers;
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
