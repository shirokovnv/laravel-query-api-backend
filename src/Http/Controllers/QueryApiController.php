<?php

namespace Shirokovnv\LaravelQueryApiBackend\Http\Controllers;

use Shirokovnv\LaravelQueryApiBackend\Http\Requests\QueryApiRequest;
use Shirokovnv\LaravelQueryApiBackend\Http\Middleware\ClientRequestId
use Shirokovnv\LaravelQueryApiBackend\Facades\LaravelQueryApiBackend;

/**
 * Example controller for processing queries
 * Class QueryApiController
 * @package Shirokovnv\LaravelQueryApiBackend\Http\Controllers
 */
class QueryApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(ClientRequestId::class);
    }

    public function runQueries(QueryApiRequest $request)
    {
        // we use empty array here, for default options (see config)
        $options = [];
        $queryRunner = LaravelQueryApiBackend::makeQueryRunnerInstance($request, $options);

        $queryResult = $queryRunner->run();
        if ($queryRunner->isLoggable()) {
            $queryRunner->saveLog();
        }

        return response()->json($queryResult);
    }

}
