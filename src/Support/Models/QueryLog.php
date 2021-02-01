<?php

namespace Shirokovnv\LaravelQueryApiBackend\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueryLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'query_chain_log_id',
        'backend_uuid',
        'query',
        'model_class_name',
        'client_query_data',
        'status',
        'error_text'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'client_query_data' => 'array',
        'error_text' => 'array'
    ];

    public function query_chain(): BelongsTo
    {
        return $this->belongsTo(QueryChainLog::class);
    }
}
