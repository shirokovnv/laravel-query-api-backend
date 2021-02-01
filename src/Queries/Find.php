<?php

namespace Shirokovnv\LaravelQueryApiBackend\Queries;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\AccessDeniedException;
use Shirokovnv\LaravelQueryApiBackend\QueryGate;

class Find extends TraceableQuery
{
    public $action_name = 'find';
    public $model_class_name;
    public $id;

    public function __construct(string $model_class_name, int $id)
    {
        $this->model_class_name = $model_class_name;
        $this->id = $id;
    }

    public function run()
    {
        $model = $this->trace(function () {
            return $this->model_class_name::findOrFail($this->id);
        });

        if (QueryGate::denies('view', $model)) {
            throw new AccessDeniedException(
                "to see {$this->model_class_name} with id {$this->id}"
            );
        }

        return $model;
    }

    public function getQueryParams(): array
    {
        return ['id' => $this->id];
    }

}
