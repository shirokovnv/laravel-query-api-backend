<?php

namespace Shirokovnv\LaravelQueryApiBackend;

use Shirokovnv\LaravelQueryApiBackend\Errors\QueryErrorFactory;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\InvalidQueryDataFormatException;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\TransactionException;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\UnknownQueryModeException;
use Shirokovnv\LaravelQueryApiBackend\Queries\QueryFactory;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Collection;
use Shirokovnv\LaravelQueryApiBackend\Support\Constants;

/**
 * Class QueryRunner
 *
 * @package Shirokovnv\LaravelQueryApiBackend
 */
class QueryRunner
{
    /**
     * @var
     */
    private $request;

    /**
     * @var Collection
     */
    private $queries;

    /**
     * @var bool
     */
    private $traceable;

    /**
     * @var bool
     */
    private $warningable;

    /**
     * @var bool
     */
    private $loggable;

    /**
     * @var string
     */
    private $query_mode;

    /**
     * @var QueryResult
     */
    private $queries_result;

    public function __construct(
        $request,
        bool $isTraceable,
        bool $isLoggable,
        bool $isWarningable
    ) {
        $this->traceable = $isTraceable;
        $this->warningable = $isWarningable;
        $this->loggable = $isLoggable;

        $this->request = $request;

        $this->parseRequestParams();
    }

    private function parseRequestParams()
    {
        $data_queries_collection = collect($this->request->query_data);
        $this->query_mode = $this->request->query_mode;

        $this->queries = $data_queries_collection->map(function ($query_data) {

            return [$query_data['key'], $this->makeQueryFromData($query_data)];
        });
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
     * Makes specific type of query (e.g.: create, update, etc.) from data array
     *
     * @param array $query_data
     * @return Queries\Create|Queries\Custom|Queries\Delete|Queries\Fetch|Queries\Find|Queries\Update
     * @throws Exceptions\UnknownActionException|Exceptions\BadQueriedClassException
     */
    public function makeQueryFromData(array $query_data)
    {
        QueryValidator::validateQueryData($query_data);

        return QueryFactory::create($query_data);
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
     * @return bool
     */
    public function isLoggable(): bool
    {
        return $this->loggable;
    }

    /**
     * @param bool $loggable
     */
    public function setLoggable(bool $loggable): void
    {
        $this->loggable = $loggable;
    }

    /**
     * @return string
     */
    public function getQueryMode(): string
    {
        return $this->query_mode;
    }

    /**
     * @param string $query_mode
     */
    public function setQueryMode(string $query_mode): void
    {
        $this->query_mode = $query_mode;
    }

    /**
     * @return Collection
     */
    public function getQueries(): Collection
    {
        return $this->queries;
    }

    /**
     * @param Collection $queries
     */
    public function setQueries(Collection $queries): void
    {
        $this->queries = $queries;
    }

    /**
     * @param array $query_data
     */
    public function addQuery(array $query_data): void
    {
        $this->queries->push($query_data);
    }

    /**
     * @return QueryResult
     */
    public function getQueriesResult(): QueryResult
    {
        return $this->queries_result;
    }

    /**
     * Runs all queries from collection, based on specific mode: transaction or multiple
     *
     * @return QueryResult
     * @throws UnknownQueryModeException
     */
    public function run(): QueryResult
    {
        if (!in_array($this->query_mode, Constants::AVAILABLE_QUERY_MODES)) {
            throw new UnknownQueryModeException();
        }

        if ($this->query_mode === 'transaction') {
            return $this->runTransaction();
        }
        if ($this->query_mode === 'multiple') {
            return $this->runMultiple();
        }
    }

    /**
     * Runs all queries in transaction mode
     *
     * @return QueryResult
     */
    public function runTransaction(): QueryResult
    {
        $this->clearResults();

        try {
            DB::transaction(function () {

                $this->runQueries();

                if (!empty($this->queries_result->getErrors())) {
                    throw new TransactionException("{uniqid}");
                }
            });
        } catch (Exception $e) {
            $this->queries_result->addError(
                '__global',
                QueryErrorFactory::createFor($e)
            );
        } finally {
            if ($this->isLoggable()) {
                $this->queries_result->setLogInstance();
            }
        }

        return $this->queries_result;
    }

    /**
     * Runs all queries in multiple mode
     *
     * @return QueryResult
     */
    public function runMultiple(): QueryResult
    {
        $this->clearResults();

        $this->runQueries();

        if ($this->isLoggable()) {
            $this->queries_result->setLogInstance();
        }

        return $this->queries_result;
    }

    /**
     * Runs all the queries
     */
    private function runQueries()
    {
        $this->queries->each(function (&$query) {
            $query_key = $query[0];

            try {
                $this->runQuery($query);
            } catch (Exception $e) {
                $query_error = QueryErrorFactory::createFor($e);
                $query_error->setTraceable($this->isTraceable());

                $query[1]->setError($query_error);

                $this->queries_result->addError(
                    $query_key,
                    $query_error
                );
            } finally {
                if ($this->isLoggable()) {
                    $query[1]->setLogInstance();
                }
            }
        });
    }

    /**
     * Do actual execution of a single query
     *
     * @param $query
     * @throws Exceptions\UnknownActionException
     */
    public function runQuery(&$query)
    {
        $query_key = $query[0];

        QueryValidator::validate(
            $query[1]->getModelClassName(),
            $query[1]->getActionName(),
            $query[1]->getQueryParams()
        );

        /**
         * Run query
         */
        $query_data = $query[1]->run();
        $query_warnings = $query[1]->getWarnings();

        if (!empty($query_warnings)) {
            $this->queries_result->addWarning($query_key, $query_warnings);
        }

        $this->queries_result->addData(
            $query_key,
            $query_data
        );

        $this->queries_result->addTrace(
            $query_key,
            $query[1]->getTrace()
        );
    }

    /**
     * Clears the results of runned queries
     */
    public function clearResults()
    {
        unset($this->queries_result);
        $this->queries_result = new QueryResult(
            $this->request,
            $this->isTraceable(),
            $this->isWarningable()
        );
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
     * Saves log about queries to database
     *
     * @throws Exception
     */
    public function saveLog(): void
    {
        if (!$this->isLoggable()) {
            throw new Exception(
                "Runner is not loggable. Probably you need to define config properly.",
                500
            );
        }

        $query_chain_log = $this->queries_result->getLogInstance();
        $actual_query_logs = $this->queries->map(function (&$query) {
            return $query[1]->getLogInstance();
        });

        QueryLogger::saveLog($query_chain_log, $actual_query_logs);
    }
}
