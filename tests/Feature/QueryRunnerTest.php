<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Shirokovnv\LaravelQueryApiBackend\Facades\LaravelQueryApiBackend;
use Shirokovnv\LaravelQueryApiBackend\QueryRunner;
use Shirokovnv\LaravelQueryApiBackend\Tests\TestCase;
use Str;

class QueryRunnerTest extends TestCase
{
    /**
     * An example of initializing query runner with proper data.
     *
     * @return void
     */
    public function testRunnerInitSuccess()
    {
        $testData = '{
            "query": "fetch",
            "key": "post",
            "type": "App\\Post",
            "params": {
                "per_page": 10,
                "page": 1,
                "parts": [
                    {
                        "kind": "where",
                        "args": {
                            "params": [
                                "id",
                                "=",
                                1
                            ]
                        }
                    },
                    {
                        "kind": "whereHas",
                        "args": {
                            "relation": "author",
                            "subquery": {
                                "query": "fetch",
                                "key": "k6o7daqzwcrkktkvltx",
                                "type": "author",
                                "params": {
                                    "parts": [
                                        {
                                            "kind": "where",
                                            "args": {
                                                "params": "name"
                                            }
                                        }
                                    ]
                                }
                            }
                        }
                    }
                ]
            }
        }';

        $request = new Request([
            'query_data' => json_decode($testData, true),
            'query_mode' => 'transaction',
            'client_request_id' => Str::uuid()
        ]);

        $runner = LaravelQueryApiBackend::makeQueryRunnerInstance($request, []);

        $this->assertTrue($runner instanceof QueryRunner);
    }
}
