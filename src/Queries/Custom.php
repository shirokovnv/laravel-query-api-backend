<?php

namespace Shirokovnv\LaravelQueryApiBackend\Queries;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\AccessDeniedException;
use Shirokovnv\LaravelQueryApiBackend\QueryGate;

class Custom extends TraceableQuery
{
    public $action_name = 'custom';
    public $model_class_name;
    public $params;

    public function __construct(string $model_class_name, array $params)
    {
        $this->model_class_name = $model_class_name;
        $this->params = $params;
    }

    public function run()
    {
        if (QueryGate::denies('custom', $this->model_class_name)) {
            throw new AccessDeniedException(
                "to interact with {$this->model_class_name}"
            );
        }

        return $this->trace(function () {
            return (new $this->model_class_name($this->params))->run();
        });
    }

    public function getQueryParams(): array
    {
        return $this->params;
    }

}
