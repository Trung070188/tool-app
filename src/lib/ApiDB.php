<?php

class ApiDB
{
    public static $queryHistory = [];
    private $pdo = null;


    public function __construct()
    {
    }

    public function connect()
    {
        if ($this->pdo === null) {
            $db =  apiGetEnv('DB_DATABASE');
            $host =  apiGetEnv('DB_HOST');
            $username =  apiGetEnv('DB_USERNAME');
            $port = apiGetEnv('DB_PORT', 3306);
            $password = apiGetEnv('DB_PASSWORD');

            $this->pdo = new \PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }


    public function query($sql, array $values = array())
    {
        self::$queryHistory[] = [$sql, $values];
        $this->connect();
        $stmt = $this->pdo->prepare($sql);

        foreach ($values as $i => &$value) {
            if (is_string($value)) {
                $stmt->bindValue($i + 1, $value, \PDO::PARAM_STR);
            } else if (is_int($value)) {
                $stmt->bindValue($i + 1, $value, \PDO::PARAM_INT);
            } else if ($value === null) {
                $stmt->bindValue($i + 1, null, \PDO::PARAM_NULL);
            } else {


                $stmt->bindValue($i + 1, $this->convertPrepareValue($value), \PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        return $stmt;
    }

    private function convertPrepareValue($value)
    {
        if (is_array($value) || $value instanceof \stdClass) {
            $value = json_encode($value);
        } else if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        } else if (is_object($value)) {
            $value = (string) $value;
        }

        return $value;
    }

    public function raw($sql, array $values = array())
    {
        return (object)[
            'sql' => $sql,
            'values' => $values
        ];
    }

    public function select($sql, array $values = array())
    {

        $stmt = $this->query($sql, $values);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->redis = null;
        return $result;
    }

    public function selectFirst($sql, array $values = array())
    {

        $stmt = $this->query($sql, $values);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $result = $result === false ? null : $result;

        $this->redis = null;
        return $result;

    }

    public function selectLists($sql, $values, $key, $value = null)
    {

        $stmt = $this->query($sql, $values);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $lists = array();

        if (!empty ($result)) {
            foreach ($result as $data) {
                $lists[$data[$key]] = ($value === null) ? $data : $data[$value];
            }
        }

        $this->redis = null;
        return $lists;
    }

    public function insert($table, array $data)
    {
        if (empty ($table)) {
            throw new \Exception('Table name is not specified');
        }

        if (empty ($data)) {
            throw new \Exception('Insert data can not be empty');
        }

        $insertStates = array();
        $valueStates = array();
        $values = array();

        foreach ($data as $k => $v) {
            $insertStates[] = "`$k`";
            $valueStates[] = '?';
            $values[] = $this->convertPrepareValue($v);
        }

        $query = array (
            'INSERT INTO `' . $table . '`(' . implode(',', $insertStates) .')',
            'VALUES(' . implode(',', $valueStates) . ')',
        );


        $sql = trim(implode(' ', $query));
        $this->query($sql, $values);

        return $this->pdo->lastInsertId();
    }

    public function update($table, array $data, array $wheres)
    {
        if (empty ($table)) {
            throw new \Exception('Table name is not specified');
        }

        if (empty ($data)) {
            throw new \Exception('Update data can not be empty');
        }

        $updateSetStates = array();
        $values = array();

        $whereState = array();

        foreach ($data as $k => $v) {
            if ($v instanceof \stdClass && isset($v->sql)) {
                $updateSetStates[] = "`$k`=" . $v->sql;

                foreach ($v->values as $_v) {
                    $values[] = $this->convertPrepareValue($_v);
                }

            } else {
                $updateSetStates[] = "`$k`=?";
                $values[] = $this->convertPrepareValue($v);
            }
        }

        foreach ($wheres as $field => $value) {
            $whereState[] = "`$field`=?";
            $values[] =  $this->convertPrepareValue($value);

        }

        $query = array (
            'UPDATE ' . "`$table`",
            'SET ' . implode(',', $updateSetStates),
            'WHERE ' . implode(' AND ', $whereState),
        );

        $sql = trim(implode(' ', $query));
        $stmt = $this->query($sql, $values);
        return $stmt->rowCount();
    }

    public function beginTransaction()
    {
        $this->connect();
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Interpolate Query:  for debug only
     * @return string The interpolated query
     */
    public static function getInterpolatedSql($query, $params) {
        $query = preg_replace('/\s+/', ' ', $query);
        $keys = array();
        $values = $params;

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_array($value) || $value instanceof \stdClass) {
                $value = json_encode($value);
            } else if ($value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            } else if (is_object($value)) {
                $value = (string) $value;
            }

            if (is_string($value)) {
                $values[$key] = "'" . self::quoteValue($value) . "'";
            }

            if (is_null($value)){
                $values[$key] = 'NULL';
            }

        }

        $query = preg_replace($keys, $values, $query, 1, $count);

        return $query;
    }

    /**
     * Quotes value, for debug only
     * @param $inp
     * @return array|mixed
     */
    public static function quoteValue($inp)
    {
        if (is_array($inp)){
            return array_map(__METHOD__, $inp);
        }

        if (!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }

}
