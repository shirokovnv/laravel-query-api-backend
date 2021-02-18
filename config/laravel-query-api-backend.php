<?php

return [
    /**
     * Query runner global options.
     * You still can define local options, when calling
     * LaravelQueryApiBackend::makeQueryRunnerInstance($request, $options).
     */
    'options' => [
        'isLoggable' => true,
        'isWarningable' => true,
        'isTraceable' => false,
    ],

];
