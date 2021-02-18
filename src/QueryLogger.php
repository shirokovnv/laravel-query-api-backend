<?php

namespace Shirokovnv\LaravelQueryApiBackend;

use Illuminate\Support\Collection;
use Shirokovnv\LaravelQueryApiBackend\Queries\TraceableQuery;
use Shirokovnv\LaravelQueryApiBackend\Support\Models\QueryChainLog;
use Shirokovnv\LaravelQueryApiBackend\Support\Models\QueryLog;

/**
 * Class QueryLogger
 * @package Shirokovnv\LaravelQueryApiBackend
 */
class QueryLogger
{
    /**
     * @param QueryResult $result
     * @return QueryChainLog
     */
    public static function initializeQueryChainLog(QueryResult &$result)
    {
        return new QueryChainLog(
            [
                'client_request_id' => $result->getRequest()->client_request_id,
                'client_query_data' => $result->getRequest()->query_data,
                'ip' => $result->getRequest()->ip(),
                'user_agent' => $result->getRequest()->server('HTTP_USER_AGENT'),
                'query_mode' => $result->getRequest()->query_mode,
                'status' => $result->getStatus(),
            ]
        );
    }

    /**
     * @param TraceableQuery $query
     * @return QueryLog
     */
    public static function initializeQueryLog(TraceableQuery &$query)
    {
        $potentialError = $query->getError();
        $potentialErrorText = ($potentialError) ? $potentialError->renderForBackend() : null;

        return new QueryLog(
            [
                'backend_uuid' => $query->getUUID(),
                'query' => $query->getActionName(),
                'model_class_name' => $query->getModelClassName(),
                'client_query_data' => $query->getQueryParams(),
                'status' => $query->getResultStatus(),
                'error_text' => $potentialErrorText,
            ]
        );
    }

    /**
     * @param QueryChainLog $chain_log
     * @param Collection $query_log_collection
     */
    public static function saveLog(QueryChainLog &$chain_log, Collection &$query_log_collection)
    {
        $chain_log->save();

        $query_log_collection->each(function (&$query_log) use ($chain_log) {
            $query_log->query_chain_log_id = $chain_log->id;
            $query_log->save();
        });
    }
}
