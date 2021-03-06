<?php

namespace Shirokovnv\LaravelQueryApiBackend\Queries;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\AccessDeniedException;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\BadArgumentException;
use Shirokovnv\LaravelQueryApiBackend\QueryGate;

class Update extends TraceableQuery
{
    public $action_name = 'update';
    public $model_class_name;
    public $id;
    public $params;

    /**
     * Update constructor.
     *
     * @param string $model_class_name
     * @param int $id
     * @param array $params
     */
    public function __construct(string $model_class_name, int $id, array $params)
    {
        $this->model_class_name = $model_class_name;
        $this->id = $id;
        $this->params = $params;
    }

    /**
     * @return mixed
     * @throws AccessDeniedException
     * @throws BadArgumentException
     */
    public function run()
    {
        $model = $this->model_class_name::findOrFail($this->id);

        if (QueryGate::denies('update', $model)) {
            throw new AccessDeniedException(
                "to update {$this->model_class_name} with id {$this->id}"
            );
        }

        return $this->trace(function () use ($model) {
            $model->update($this->params);

            return $model;
        });
    }

    /**
     * @return array|int[]
     */
    public function getQueryParams(): array
    {
        return ['id' => $this->id] + $this->params;
    }
}
