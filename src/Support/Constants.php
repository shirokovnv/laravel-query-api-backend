<?php


namespace Shirokovnv\LaravelQueryApiBackend\Support;


class Constants
{
    // constants for queries
    public const AVAILABLE_QUERY_MODES = ['transaction', 'multiple'];

    public const AVAILABLE_MODEL_PARENT_CLASSES = [
        "Illuminate\Database\Eloquent\Model",
        "Illuminate\Foundation\Auth\User"
    ];

    public const AVAILABLE_CUSTOM_QUERY_INTERFACES = [
        "Shirokovnv\LaravelQueryApiBackend\Support\Runnable"
    ];
}
