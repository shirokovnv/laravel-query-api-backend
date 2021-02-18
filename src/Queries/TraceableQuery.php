<?php

namespace Shirokovnv\LaravelQueryApiBackend\Queries;

use DB;
use Shirokovnv\LaravelQueryApiBackend\Errors\QueryError;
use Shirokovnv\LaravelQueryApiBackend\QueryLogger;
use Shirokovnv\LaravelQueryApiBackend\Support\Models\QueryLog;
use Shirokovnv\LaravelQueryApiBackend\Support\Runnable;
use Str;

abstract class TraceableQuery implements Runnable
{
    private const RESULT_STATUS_SUCCESS = 'success';
    private const RESULT_STATUS_FAILED = 'failed';

    /**
     * @var array|null
     */
    protected $trace;

    /**
     * @var array|null
     */
    protected $warnings;

    /**
     * @var QueryError|null
     */
    protected $error;

    /**
     * @var string
     */
    protected $model_class_name;

    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var array|null
     */
    protected $params;

    /**
     * @var string
     */
    protected $action_name;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var QueryLog|null
     */
    protected $log_instance;

    public function __construct()
    {
        $this->clearWarnings();
        $this->clearTrace();
        $this->makeUUID();
    }

    /**
     * @return string
     */
    public function getUUID(): string
    {
        return $this->uuid;
    }

    /**
     * Generates unique string, representing backend request ID.
     */
    public function makeUUID(): void
    {
        $this->uuid = Str::uuid();
    }

    /**
     * @return string
     */
    public function getResultStatus(): string
    {
        return ($this->error) ? self::RESULT_STATUS_FAILED : self::RESULT_STATUS_SUCCESS;
    }

    /**
     * @return array|null
     */
    public function getTrace(): ?array
    {
        return $this->trace;
    }

    /**
     * @param array|null $trace
     */
    public function setTrace(?array $trace): void
    {
        $this->trace = $trace;
    }

    /**
     * @return string
     */
    public function getModelClassName(): string
    {
        return $this->model_class_name;
    }

    /**
     * @param string $model_class_name
     */
    public function setModelClassName(string $model_class_name): void
    {
        $this->model_class_name = $model_class_name;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @param array|null $params
     */
    public function setParams(?array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->action_name;
    }

    /**
     * @param string $action_name
     */
    public function setActionName(string $action_name): void
    {
        $this->action_name = $action_name;
    }

    public function clearWarnings(): void
    {
        $this->warnings = [];
    }

    /**
     * Clears traced SQL-query.
     */
    public function clearTrace(): void
    {
        $this->trace = [];
    }

    /**
     * @return array|null
     */
    public function getWarnings(): ?array
    {
        return $this->warnings;
    }

    /**
     * @param mixed $warning
     */
    public function addWarning($warning)
    {
        $this->warnings[] = $warning;
    }

    /**
     * @return QueryError|null
     */
    public function getError(): ?QueryError
    {
        return $this->error;
    }

    /**
     * @param QueryError|null $error
     */
    public function setError(?QueryError $error): void
    {
        $this->error = $error;
    }

    /**
     * Traces SQL-query and fixes result, executed in $fn.
     *
     * @param callable $fn
     * @return mixed
     */
    public function trace($fn)
    {
        DB::enableQueryLog();
        DB::flushQueryLog();
        $result = $fn();
        $this->addTracedSQL(DB::getQueryLog());
        DB::disableQueryLog();

        return $result;
    }

    /**
     * @param mixed $sql_log
     */
    public function addTracedSQL($sql_log)
    {
        foreach ($sql_log as $sql) {
            $query = $sql['query'];
            $bindings = $sql['bindings'];
            $time = $sql['time'];

            $params = array_map(function ($item) {
                return "'{$item}'";
            }, $bindings);

            $this->trace[] = [
                'sql' => Str::replaceArray('?', $params, $query),
                'time' => $time,
            ];
        }
    }

    /**
     * Initializes log.
     */
    public function setLogInstance(): void
    {
        $this->log_instance = QueryLogger::initializeQueryLog($this);
    }

    /**
     * @return QueryLog|null
     */
    public function getLogInstance(): ?QueryLog
    {
        return $this->log_instance;
    }

    /**
     * @return mixed
     */
    abstract public function run();

    /**
     * @return array
     */
    abstract public function getQueryParams(): array;
}
