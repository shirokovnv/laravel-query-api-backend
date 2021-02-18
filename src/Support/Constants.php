<?php

namespace Shirokovnv\LaravelQueryApiBackend\Support;

/**
 * Class Constants.
 */
class Constants
{
    // constants for queries
    public const AVAILABLE_ACTIONS = ['create', 'custom', 'delete', 'fetch', 'find', 'update'];

    public const AVAILABLE_QUERY_MODES = ['transaction', 'multiple'];

    public const AVAILABLE_MODEL_PARENT_CLASSES = [
        "Illuminate\Database\Eloquent\Model",
        "Illuminate\Foundation\Auth\User",
    ];

    public const AVAILABLE_CUSTOM_QUERY_INTERFACES = [
        "Shirokovnv\LaravelQueryApiBackend\Support\Runnable",
    ];

    /**
     * Maximum level of calling subqueries.
     */
    public const MAXIMUM_DEPTH = 7;
    /**
     * Maximum number of fetched elements.
     */
    public const PER_PAGE_MAX_LIMIT = 1000;
}
