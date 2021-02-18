<?php

namespace Shirokovnv\LaravelQueryApiBackend;

use Shirokovnv\LaravelQueryApiBackend\Support\Models\QueryChainLog;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Class QueryResult
 *
 * @package Shirokovnv\LaravelQueryApiBackend
 */
class QueryResult implements JsonSerializable, Arrayable
{
    /**
     * Success status for runned query
     */
    private const STATUS_SUCCESS = 'success';
    /**
     * Failed status for runned query
     */
    private const STATUS_FAILED = 'failed';
    /**
     * @var array
     */
    private $data;
    /**
     * @var array
     */
    private $errors;
    /**
     * @var array
     */
    private $warnings;
    /**
     * @var array
     */
    private $trace;
    /**
     * @var bool
     */
    private $traceable;
    /**
     * @var bool
     */
    private $warningable;
    /**
     * @var mixed
     */
    private $request;

    /**
     * @var QueryChainLog|null
     */
    private $log_instance;

    /**
     * QueryResult constructor.
     *
     * @param $request
     * @param bool $isTraceable
     * @param bool $isWarningable
     */
    public function __construct(
        $request,
        bool $isTraceable,
        bool $isWarningable
    ) {
        $this->request = $request;
        $this->traceable = $isTraceable;
        $this->warningable = $isWarningable;

        $this->data = [];
        $this->errors = [];
        $this->warnings = [];
        $this->trace = [];
    }

    /**
     * @return mixed
     */
    public function getRequest(): mixed
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request): void
    {
        $this->request = $request;
    }

    /**
     * @param string $key
     * @param $data
     */
    public function addData(string $key, $data): void
    {
        $this->data[$key] = [
            'content' => $data
        ];
    }

    /**
     * @param string $key
     * @param $error
     */
    public function addError(string $key, $error): void
    {
        $this->errors[$key] = $error;
    }

    /**
     * @param string $key
     * @param $warning
     */
    public function addWarning(string $key, $warning): void
    {
        $this->warnings[$key] = $warning;
    }

    /**
     * @param string $key
     * @param $trace
     */
    public function addTrace(string $key, $trace)
    {
        $this->trace[$key] = $trace;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [
            'data' => $this->getData(),
            'errors' => $this->getErrors(),
        ];

        if ($this->isTraceable()) {
            $result['trace'] = $this->getTrace();
        }

        if ($this->isWarningable()) {
            $result['warnings'] = $this->getWarnings();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isTraceable(): bool
    {
        return $this->traceable;
    }

    /**
     * @param bool $traceable
     */
    public function setTraceable(bool $traceable): void
    {
        $this->traceable = $traceable;
    }

    /**
     * @return array
     */
    public function getTrace(): array
    {
        return $this->trace;
    }

    /**
     * @return bool
     */
    public function isWarningable(): bool
    {
        return $this->warningable;
    }

    /**
     * @param bool $warningable
     */
    public function setWarningable(bool $warningable): void
    {
        $this->warningable = $warningable;
    }

    /**
     * @return array
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return (!empty($this->errors));
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (!$this->hasErrors()) ? self::STATUS_SUCCESS : self::STATUS_FAILED;
    }

    /**
     * Initializes log for all runned queries
     */
    public function setLogInstance(): void
    {
        $this->log_instance = QueryLogger::initializeQueryChainLog($this);
    }

    /**
     * @return QueryChainLog|null
     */
    public function getLogInstance(): ?QueryChainLog
    {
        return $this->log_instance;
    }
}
