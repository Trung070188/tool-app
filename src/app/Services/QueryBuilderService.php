<?php

namespace App\Services;

use App\Exceptions\QueryBuilderServiceException;
use App\Services\QueryBuilderService\QueryBuilderObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryBuilderService
{
    private int $databaseId;
    private \stdClass $database;
    private static array $whiteListTables = [];

    private static array $whiteListFunctions = [];

    private static array $whiteListOperators = [];

    private static array $whiteListFunctionsArgs = [];
    private static array $whiteListSeparators = [];

    public static function inWhiteListOperators($operator): bool {
        return isset(self::$whiteListOperators[$operator]);
    }

    public static function inWhiteListFunctions($functionName): bool {
        return isset(self::$whiteListFunctions[$functionName]);
    }

    public static function inWhiteListFunctionArgs($arg): bool {
        return isset(self::$whiteListFunctionsArgs[$arg]);
    }

    public static function inWhiteListTables($table): bool {
        return isset(self::$whiteListTables[$table]);
    }

    public static function inWhiteListSeparators($table): bool {
        return isset(self::$whiteListSeparators[$table]);
    }

    private static function prepare() {
        $config = config('querybuilder.whitelist');

        self::$whiteListTables = array_flip_value($config['tables'], true);
        self::$whiteListFunctions = array_flip_value($config['functions'], true);
        self::$whiteListFunctionsArgs = array_flip_value($config['args'], true);
        self::$whiteListSeparators = array_flip_value($config['separators'], true);
        self::$whiteListOperators = array_flip_value($config['operators'], true);
        return $config;
    }

    public function handle(Request $request) {
        self::prepare();
        /*$this->databaseId = (int) $request->get('db');

        $env = config_env('REPORT_ENV');
        if ($this->databaseId === 0) {
            $database = DB::table('db_connections')
                ->selectRaw('id,name,dbname')
                ->where('type', 'report')
                ->where('env', $env)
                ->first();
        }  else {
            $database = DB::table('db_connections')
                ->selectRaw('id,name, dbname')
                ->where('id', $this->databaseId)
                ->where('type', 'report')
                ->where('env', $env)
                ->first();
        }


        if (!$database) {
            return [
                'code' => 5,
                'message' => "[$env] DatabaseID: " . $this->databaseId . ' not found'
            ];
        }

        $this->database = $database;*/

        $queryObject = null;

        try {
            $data = $request->all();
            if (isset($data['query'])) {
                $queryObject = new QueryBuilderObject($data['query']);
                return $queryObject->process();
            }

            if (isset($data['queries'])) {
                return $this->processMany($data['queries']);
            }
        } catch (QueryBuilderServiceException $exception) {
            $response =  [
                'code' => 500,
                'message' => $exception->getMessage(),
            ];

            if ($queryObject) {
                $builder = $queryObject->getQueryBuilder();
                if ($builder) {
                    $response['sql'] = [
                        $builder->toSql(),
                        $builder->getBindings()
                    ];
                }

            }

            return $response;
        } catch (\Throwable $ex) {
            return [
                'code' => 2,
                'message' => $ex->getMessage(),
                'trace' => exception_truncate($ex->getTraceAsString())
            ];
        }
    }

    private function processMany(array $queries): array {
        $results = [];
        foreach ($queries as $query) {
            $queryObject = null;
            try {
                $queryObject = new QueryBuilderObject($query);
                $results[] = $queryObject->process();
            } catch (\Exception $ex) {
                $response = [
                    'code' => 500,
                    'message' => $ex->getMessage(),
                    'trace' => exception_truncate($ex->getTraceAsString())
                ];

                if ($queryObject) {
                    $builder = $queryObject->getQueryBuilder();
                    if ($builder) {
                        $response['sql'] = [
                            $builder->toSql(),
                            $builder->getBindings()
                        ];
                    }

                }

                $results[] = $response;
            }
        }

        return $results;
    }

}
