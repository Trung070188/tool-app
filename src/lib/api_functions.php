<?php

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});




define('ROOT', dirname(__DIR__));

function apiDump(...$args) {
    $isCLI = !isset($_SERVER['REQUEST_URI']);
    if ($isCLI) {
        foreach ($args as $arg) {
            echo apiNiceVar($arg);
            echo PHP_EOL . "-------------------------------------------------" . PHP_EOL;
        }
    } else {
        echo "<pre>";

        foreach ($args as $arg) {
            echo apiNiceVar($arg);
            echo "<br>-------------------------------------------------<br/>";
        }
        echo "</pre>";
    }

    die;
}

if (!function_exists('dd')) {
    function dd(...$args) {
        apiDump(...$args);
    }
}

function apiNiceVar($input) {
    if (is_string($input)) {
        return "(string:" . strlen($input) . ')' . $input;
    }

    if (is_array($input)) {
        return "(array:" . count($input) . ')' . json_encode($input, JSON_PRETTY_PRINT);
    }

    if (is_object($input)) {
        $class = get_class($input);
        return "($class)" . json_encode($input, JSON_PRETTY_PRINT);
    }

    $type = gettype($input);
    return "($type)" . json_encode($input, JSON_PRETTY_PRINT);
}


function apiGetEnv(string $key, $defaultValue = null)
{
    static $result;
    if (!$result) {
        $contents = file_get_contents(ROOT . '/.env');
        $lines = explode("\n", $contents);
        $result = [];
        $valueMap = [
            "true" => true,
            "false" => false,
            "null" => null,
        ];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line) {
                $t = explode("=", $line);
                $k = $t[0];
                $v = $t[1] ?? null;
                if (isset($valueMap[$v])) {
                    $result[$k] = $valueMap[$v];
                } else if (is_numeric($v)) {
                    $result[$k] = 0 + $v;
                } else {
                    $result[$k] = $v;
                }
            }
        }
    }



    return $result[$key] ?? $defaultValue;
}

function apiWriteLog($data, $filename = 'error'): void
{
    if ($data instanceof \Throwable) {
        $data = get_class($data) . ": " . $data->getMessage() . "\n" . $data->getTraceAsString() . "\n";
    } else {
        $data = apiNiceVar($data);
    }

    $date = date('Y-m-d');
    $time = date('Y-m-d H:i:s');


    $logDir = ROOT . '/storage/logs/api';

    if (!file_exists($logDir)) {
        mkdir($logDir, 0777);
    }

    $file = fopen($logDir . '/'. $filename . '-'.  $date . '.log', 'a+');
    fwrite($file, '----' . $time . "----\n");
    fwrite($file, $data);
    fclose($file);
}

function apiGetDb(): ApiDB
{
    static $db;
    if ($db) {
        return $db;
    }

    $db = new ApiDB();

    return $db;
}

function apiRedisInit(): \Redis
{
    static $redis;
    if (isset($redis)) {
        return $redis;
    }

    $redis = new \Redis();
    $redis->connect(apiGetEnv('REDIS_HOST', 'localhost'), apiGetEnv('REDIS_PORT', '6379'));
    $redis->select(apiGetEnv('REDIS_DB', 0));
    return $redis;
}

function apiGetClientIp()
{
    static $ip;

    if (isset ($ip)) {
        return $ip;
    }

    if (!empty ($_SERVER['HTTP_CLIENT_IP']) ) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty ($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (!empty ($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } else if ( !empty ($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if ( !empty ($_SERVER['HTTP_FORWARDED']) ) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } else if( !empty ($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = 'UNKNOWN';
    }


    return $ip;
}

function apiLogResponse($response, \Throwable $e = null, $httpCode = 200, $params = null)
{
    try {
        if (is_string($response)) {
            $entry['response'] = $response;
        } else {
            $entry['response'] = json_encode($response, JSON_PRETTY_PRINT);
        }

        $entry['uri'] = $_SERVER['REQUEST_URI'];
        $entry['method'] = $_SERVER['REQUEST_METHOD'];
        $entry['request_headers'] = json_encode(getallheaders(), JSON_PRETTY_PRINT);
        $entry['event_type'] = 'Info';
        $entry['http_code'] = $httpCode;
        $entry['time'] = date('Y-m-d H:i:s');
        $entry['ip'] = apiGetClientIp();
        $params = [];
        if (empty($params)) {
            if (!empty($_POST)) {
                $params = $_POST;
            } else if (!empty($_GET)) {
                $params = $_GET;
            } else {
                $params = file_get_contents("php://input");
            }
        }
        $entry['payload'] = is_array($params) ? json_encode($params, JSON_PRETTY_PRINT) : $params;
        $entry['query'] = $_GET;

        $queries = ApiDB::$queryHistory;

        if (!empty ($queries)) {
            $sqlQueries = [];

            foreach ($queries as $query) {
                $sqlQueries[] = ApiDB::getInterpolatedSql($query[0], $query[1]);
            }

            $entry['db_query'] = json_encode($sqlQueries, JSON_PRETTY_PRINT);
        }

        if ($e !== null) {
            $entry['event_type'] = 'Error';
            $entry['exception'] = get_class($e);
            $entry['message'] = $e->getMessage();
            $entry['trace'] = $e->getTraceAsString();
        }

        $db = apiGetDb();

        $db->insert('api_logs', $entry);
    } catch (\Throwable $ex) {
        apiWriteLog($ex);
    }
}
