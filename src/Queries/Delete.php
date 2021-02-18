<?php

namespace Shirokovnv\LaravelQueryApiBackend\Queries;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\AccessDeniedException;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\BadArgumentException;
use Shirokovnv\LaravelQueryApiBackend\QueryGate;

class Delete extends TraceableQuery
{
    public $action_name = 'delete';
    public $model_class_name;
    public $id;

    /**
     * Delete constructor.
     *
     * @param string $model_class_name
     * @param int $id
     */
    public function __construct(string $model_class_name, int $id)
    {
        $this->model_class_name = $model_class_name;
        $this->id = $id;
    }

    /**
     * @return mixed
     * @throws AccessDeniedException
     * @throws BadArgumentException
     */
    public function run()
    {
        $model = $this->model_class_name::findOrFail($this->id);

        if (QueryGate::denies('delete', $model)) {
            throw new AccessDeniedException(
                "to delete {$this->model_class_name} with id {$this->id}"
            );
        }

        return $model->delete();
    }

    /**
     * @return int[]
     */
    public function getQueryParams(): array
    {
        return ['id' => $this->id];
    }
}
