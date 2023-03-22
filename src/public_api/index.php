<?php
require '../api/lib/api_functions.php';
require '../api/lib/ApiDB.php';
require '../api/routes/api_campaign_install.php';
require '../api/routes/api_ping.php';

function main(): array
{
    list($requestUri) = explode('?', $_SERVER['REQUEST_URI']);
    $routeMap = require_once ROOT . '/api/routes.php';


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

    $filename = $routeMap[$requestUri];
    $parts = array_map('ucfirst', explode('_', $filename));
    require_once  ROOT . '/api/routes/api_' . $filename . '.php';
    $callback =  'api'. implode("", $parts);


    if (!function_exists($callback)) {
        http_response_code(404);

        return [
            'code' => 404,
            'message' => 'Function not found: ' . $callback
        ];
    }

    return $callback($params ?? [], $query ?? []);
}



$exception = null;
$httpCode = 200;

try {
    $response = main();
} catch (\Throwable $ex) {
    $APP_DEBUG = apiGetEnv('APP_DEBUG');

    $exception = $ex;
    apiWriteLog($ex);
    $httpCode = 503;
    http_response_code(503);

    $response = [
        'code' => 503,
        'message' => 'Internal server error'
    ];
    if ($APP_DEBUG) {
        $response['exception'] = [
            'class' => get_class($ex),
            'message' => $ex->getMessage(),
            'trace' => explode("\n", $ex->getTraceAsString())
        ];
    }
}

apiLogResponse($response, $exception, $httpCode);

header('Content-Type: application/json');
echo json_encode($response);
