<?php

namespace Shirokovnv\LaravelQueryApiBackend;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\UnknownActionException;
use Shirokovnv\LaravelQueryApiBackend\Support\ShouldValidate;
use Illuminate\Support\Facades\Validator;

class QueryValidator
{
    /**
     * Validates specific model
     * We store validation rules in FormRequests, e.g.
     * for model App\Models\User , App\Http\Requests\Models\UserRequest should exist.
     * Otherwise ClassNotFoundException will be thrown
     * @param string $model_class_name
     * @param string $action_name
     * @param array $params
     * @return bool
     * @throws UnknownActionException
     */
    public static function validate(string $model_class_name, string $action_name, array $params)
    {

        if (self::shouldValidateFor($model_class_name, $action_name)) {
            $form_request_class = self::getFormRequestClassNameForModel($model_class_name);
            $form_request = new $form_request_class($params);

            $form_request->setMethod(self::getRequestMethodNameForAction($action_name));

            $validator = Validator::make(
                $params,
                $form_request->rules()
            );

            $validator->validate();
        }

        return true;
    }

    /**
     * Defines whether specific class should be validated for specific action
     * Class should implement Shirokovnv\LaravelQueryApiBackend\Support\ShouldValidate interface
     * @param string $model_class_name
     * @param string $action_name
     * @return bool
     */
    public static function shouldValidateFor(string $model_class_name, string $action_name): bool
    {
        $implements = class_implements($model_class_name);
        if (!in_array(ShouldValidate::class, $implements)) {
            return false;
        }

        $should_validate_actions = $model_class_name::shouldValidateActions();
        if (empty($should_validate_actions)) {
            return false;
        }
        if (in_array("*", $should_validate_actions)) {
            return true;
        }
        return (in_array($action_name, $should_validate_actions));
    }

    /**
     * e.g. for App\Models\User it returns App\Http\Requests\Models\UserRequest
     * @param string $model_class_name
     * @return string
     */
    public static function getFormRequestClassNameForModel(string $model_class_name): string
    {

        $exploded_name = explode('\\', $model_class_name);
        $short_class_name = end($exploded_name);

        $namespace = substr($model_class_name, 0, strrpos($model_class_name, '\\'));
        $namespace_without_app = str_replace('App\\', '', $namespace);

        $request_namespace = 'App\Http\Requests\\' . $namespace_without_app;
        $request_class_name = $request_namespace . "\\" . $short_class_name . 'Request';

        return $request_class_name;
    }

    /**
     * Converts specific action name to http request method name, based on REST
     * @param string $action_name
     * @return string
     * @throws UnknownActionException
     */
    public static function getRequestMethodNameForAction(string $action_name): string
    {
        switch ($action_name) {
            case 'create':
                return 'POST';
                break;

            case 'update':
                return 'PATCH';
                break;

            case 'delete':
                return 'DELETE';
                break;

            case 'fetch':
                return 'GET';
                break;

            case 'find':
                return 'GET';
                break;

            case 'custom':
                return 'POST';
                break;
        }

        throw new UnknownActionException($action_name);
    }

    /**
     * @param array $query_data
     */
    public static function validateQueryData(array $query_data): void
    {
        $validator = Validator::make($query_data, [
            'type' => 'required|string',
            'key' => 'required|string',
            'query' => 'required|string'
        ]);

        $validator->validate();
    }
}
