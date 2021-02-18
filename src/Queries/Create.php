<?php

namespace Shirokovnv\LaravelQueryApiBackend\Queries;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\AccessDeniedException;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\BadArgumentException;
use Shirokovnv\LaravelQueryApiBackend\QueryGate;

class Create extends TraceableQuery
{
    public $action_name = 'create';
    public $model_class_name;
    public $params;

    /**
     * Create constructor.
     *
     * @param string $model_class_name
     * @param array $params
     */
    public function __construct(string $model_class_name, array $params)
    {
        $this->model_class_name = $model_class_name;
        $this->params = $params;
    }

    /**
     * @return mixed
     * @throws AccessDeniedException
     * @throws BadArgumentException
     */
    public function run()
    {
        if (QueryGate::denies('create', $this->model_class_name)) {
            throw new AccessDeniedException("to create $this->model_class_name");
        }

        return $this->trace(function () {
            return $this->model_class_name::create($this->params);
        });
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->params;
    }
}
