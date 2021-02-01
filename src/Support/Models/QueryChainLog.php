<?php

namespace Shirokovnv\LaravelQueryApiBackend\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QueryChainLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_request_id',
        'client_query_data',
        'ip',
        'user_agent',
        'query_mode',
        'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'client_query_data' => 'array'
    ];

    public function query_logs(): HasMany
    {
        return $this->hasMany(QueryLog::class);
    }
}
