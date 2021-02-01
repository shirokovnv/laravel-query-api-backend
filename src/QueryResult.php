<?php


namespace Shirokovnv\LaravelQueryApiBackend;

use Shirokovnv\LaravelQueryApiBackend\Support\Models\QueryChainLog;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class QueryResult implements JsonSerializable, Arrayable
{
    private const STATUS_SUCCESS = 'success';
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

    public function __construct($request,
                                bool $isTraceable,
                                bool $isWarningable)
    {
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
    public function getRequest()
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
    public function addData(string $key, $data)
    {
        $this->data[$key] = [
            'content' => $data
        ];
    }

    /**
     * @param string $key
     * @param $error
     */
    public function addError(string $key, $error)
    {
        $this->errors[$key] = $error;
    }

    /**
     * @param string $key
     * @param $warning
     */
    public function addWarning(string $key, $warning)
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

    public function hasErrors(): bool
    {
        return (!empty($this->errors));
    }

    public function getStatus(): string
    {
        return (!$this->hasErrors()) ? self::STATUS_SUCCESS : self::STATUS_FAILED;
    }

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
