<?php

namespace Shirokovnv\LaravelQueryApiBackend\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\AccessDeniedException;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\BadArgumentException;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\MaximumDepthReachedException;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\UnknownQueryPartException;
use Shirokovnv\LaravelQueryApiBackend\QueryGate;
use Shirokovnv\LaravelQueryApiBackend\Support\Constants;
use Shirokovnv\LaravelQueryApiBackend\Support\RelationChain;
use Str;

/**
 * Class Fetch.
 */
class Fetch extends TraceableQuery
{
    /**
     * @var string
     */
    public $action_name = 'fetch';

    /**
     * @var string
     */
    public $model_class_name;

    /**
     * @var mixed
     */
    public $args;

    /**
     * @var array
     */
    public $kindMap;

    /**
     * @var mixed
     */
    public $query;

    /**
     * @var int|null
     */
    public $page;

    /**
     * @var int|null
     */
    public $per_page;

    /**
     * @var array
     */
    public $relation_chains;

    public function __construct(string $model_class_name, array $args)
    {
        $this->model_class_name = $model_class_name;
        $this->args = $args['parts'];
        $this->relation_chains = [];
        $this->setPaginationByArgs($args);
        $this->buildKindMap();
        parent::__construct();
    }

    /**
     * @param array $args
     */
    public function setPaginationByArgs(array $args)
    {
        if (isset($args['per_page'])) {
            $this->per_page = min(Constants::PER_PAGE_MAX_LIMIT, $args['per_page']);
        }

        if (isset($args['page'])) {
            $this->page = $args['page'];
        }
    }

    /**
     * Builds map of usable conditions in fetch query.
     */
    private function buildKindMap()
    {
        $this->kindMap = [
            'with' => function ($q, $args) {

                /**
                 * build relation chain based on dot notation, e.g.: posts.user.comments.
                 */
                foreach ($args['table_name_list'] as $relation_name) {
                    // If we have nested relations
                    $nested_relations = explode('.', $relation_name);

                    $this->relation_chains[$relation_name] =
                        new RelationChain($this->model_class_name, $nested_relations);
                }

                return $q->with($args['table_name_list']);
            },

            'where' => function ($q, $args) {
                return $q->where([$args['params']]);
            },

            'whereIn' => function ($q, $args) {
                return $q->whereIn($args['key'], $args['values']);
            },

            'orWhere' => function ($q, $args) {
                return $q->orWhere([$args['params']]);
            },

            'whereHas' => function ($q, $args, $depth = 1) {
                if (! $args['subquery']) {
                    return $q->whereHas($args['relation']);
                }

                return $q->whereHas($args['relation'], function ($qw) use ($args, $depth) {
                    $this->iterateQueryArgs($qw, $args['subquery']['params']['parts'], $depth);
                });
            },

            'orWhereHas' => function ($q, $args, $depth = 1) {
                if (! $args['subquery']) {
                    return $q->orWhereHas($args['relation']);
                }

                return $q->whereHas($args['relation_name'], function ($qw) use ($args, $depth) {
                    $this->iterateQueryArgs($qw, $args['subquery']['parts'], $depth);
                });
            },

            'whereNotIn' => function ($q, $args) {
                return $q->whereNotIn($args['key'], $args['values']);
            },

            'whereDoesntHave' => function ($q, $args, $depth = 1) {
                if (! $args['subquery']) {
                    return $q->whereDoesntHave($args['relation']);
                }

                return $q->whereDoesntHave($args['relation'], function ($qw) use ($args, $depth) {
                    $this->iterateQueryArgs($qw, $args['subquery']['parts'], $depth);
                });
            },

            'orWhereDoesntHave' => function ($q, $args, $depth = 1) {
                if (! $args['subquery']) {
                    return $q->orWhereDoesntHave($args['relation']);
                }

                return $q->orWhereDoesntHave($args['relation'], function ($qw) use ($args, $depth) {
                    $this->iterateQueryArgs($qw, $args['subquery']['parts'], $depth);
                });
            },

            'whereNotNull' => function ($q, $args) {
                return $q->whereNotNull($args['column_name']);
            },

            'orWhereNotNull' => function ($q, $args) {
                return $q->orWhereNotNull($args['column_name']);
            },

            'whereNull' => function ($q, $args) {
                return $q->whereNull($args['column_name']);
            },

            'orWhereNull' => function ($q, $args) {
                return $q->orWhereNull($args['column_name']);
            },

            'whereColumn' => function ($q, $args) {
                return $q->whereColumn($args['columns_list']);
            },

            'whereExists' => function ($q, $args) {
                return $q->whereExists(function ($query) use ($args) {
                    return $query->select($args['select'])
                        ->from($query['from'])
                        ->whereRaw($args['where']);
                });
            },

            'scope' => function ($q, $args) {
                $scopeName = ucfirst(Str::camel($args['name']));
                $scopeParams = $args['params'];

                if (gettype($scopeParams) === 'array') {
                    return $q->{$scopeName}(...$scopeParams);
                }

                return $q->{$scopeName}();
            },

            'limit' => function ($q, $args) {
                return $q->limit($args['limit']);
            },

            'offset' => function ($q, $args) {
                return $q->offset($args['offset']);
            },

            'select' => function ($q, $args) {
                return $q->select($args['columns']);
            },
        ];
    }

    /**
     * @param $query
     * @param $args
     * @param int $depth
     * @throws MaximumDepthReachedException
     * @throws UnknownQueryPartException
     */
    public function iterateQueryArgs($query, $args, int $depth = 1)
    {
        if ($depth > Constants::MAXIMUM_DEPTH) {
            throw new MaximumDepthReachedException();
        }

        foreach ($args as $query_part) {
            $kind = $query_part['kind'];
            if (! $this->isKindOfQueryPartExists($kind)) {
                throw new UnknownQueryPartException($kind);
            }
            $this->kindMap[$kind]($query, $query_part['args'], $depth + 1);
        }
    }

    /**
     * @param string $kind
     * @return bool
     */
    public function isKindOfQueryPartExists(string $kind)
    {
        return array_key_exists($kind, $this->kindMap);
    }

    /**
     * Build all query parts together and runs the query.
     *
     * @return mixed
     * @throws AccessDeniedException
     * @throws BadArgumentException|UnknownQueryPartException
     */
    public function run()
    {
        if (QueryGate::denies('viewAny', $this->model_class_name)) {
            throw new AccessDeniedException("to see {$this->model_class_name}");
        }

        return $this->trace(function () {
            $this->query = $this->model_class_name::query();

            foreach ($this->args as $query_part) {
                $kind = $query_part['kind'];
                if (! $this->isKindOfQueryPartExists($kind)) {
                    throw new UnknownQueryPartException($kind);
                }
                $this->query = $this->kindMap[$kind]($this->query, $query_part['args']);
            }

            $per_page = $this->per_page ?? Constants::PER_PAGE_MAX_LIMIT;
            $page = $this->page ?? 1;
            $offset = $page ? $per_page * ($this->page - 1) : 0;
            $total = $this->query->count();
            $collection = $this->query->offset($offset)->limit($per_page)->get();

            $filtered_collection = $this->getFilteredResultByPermissions($collection);
            $paginator = new LengthAwarePaginator(
                $filtered_collection,
                $total,
                $per_page,
                $page,
                ['path' => url()->current()]
            );

            return $paginator;
        });
    }

    /**
     * @param $result
     * @return mixed
     * @throws BadArgumentException
     */
    public function getFilteredResultByPermissions($result)
    {
        $collection = $result->map(function ($item) {

            // check all permissions
            foreach ($this->relation_chains as $key => $chain) {
                QueryGate::recursiveSetItemPermissions($item, $chain->getRoot(), $this);
            }

            return $item;
        });

        return $collection;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->args;
    }
}
