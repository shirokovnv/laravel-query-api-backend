<?php

namespace Shirokovnv\LaravelQueryApiBackend\Queries;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\UnknownActionException;

class QueryFactory
{
    const AVAILABLE_ACTIONS = ['create', 'custom', 'delete', 'fetch', 'find', 'update'];

    public static function create(array $query_data)
    {

        if (!in_array($query_data['query'], self::AVAILABLE_ACTIONS)) {
            throw new UnknownActionException("{$query_data[query]}");
        }

        switch ($query_data['query']) {
            case 'create':
                return new Create($query_data['type'], $query_data['params']);
                break;

            case 'custom':
                return new Custom($query_data['type'], $query_data['params']);
                break;

            case 'delete':
                return new Delete($query_data['type'], $query_data['params']['id']);
                break;

            case 'fetch':
                return new Fetch($query_data['type'], $query_data['params']);
                break;

            case 'find':
                return new Find($query_data['type'], $query_data['params']['id']);
                break;

            case 'update':
                return new Update($query_data['type'], $query_data['params']['id'], $query_data['params']['data']);
                break;
        }
    }
}
