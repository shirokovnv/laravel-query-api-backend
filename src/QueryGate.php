<?php

namespace Shirokovnv\LaravelQueryApiBackend;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\BadArgumentException;
use Shirokovnv\LaravelQueryApiBackend\Queries\TraceableQuery;
use Shirokovnv\LaravelQueryApiBackend\Support\RelationNode;
use Shirokovnv\LaravelQueryApiBackend\Support\ShouldAuthorize;
use Gate;

/**
 * Class QueryGate
 *
 * @package Shirokovnv\LaravelQueryApiBackend
 */
class QueryGate
{
    private const ASTERISK = "*";

    /**
     * @var array
     */
    private static $DROP_CANDIDATES = [];

    /**
     * @param string $ability
     * @param array|mixed $arguments
     * @return bool
     * @throws BadArgumentException
     */
    public static function denies($ability, $arguments = [])
    {
        if (self::shouldAuthorizeFor($ability, $arguments)) {
            return Gate::denies($ability, $arguments);
        }
        return false;
    }

    /**
     * Defines whether specific class should be authorized for specific abilities
     * Class should implement Shirokovnv\LaravelQueryApiBackend\Support\ShouldAuthorize interface for checking
     * Otherwise false will be returned
     *
     * @param $ability
     * @param array|mixed $arguments
     * @return bool
     * @throws BadArgumentException
     */
    public static function shouldAuthorizeFor($ability, $arguments = [])
    {
        $model_class_name = self::getModelClassNameFromArguments($arguments);

        $implements = class_implements($model_class_name);
        if (!in_array(ShouldAuthorize::class, $implements)) {
            return false;
        }

        $should_authorize_abilities = $model_class_name::shouldAuthorizeAbilities();
        if (empty($should_authorize_abilities)) {
            return false;
        }
        if (in_array(self::ASTERISK, $should_authorize_abilities)) {
            return true;
        }
        return (in_array($ability, $should_authorize_abilities));
    }

    /**
     * @param array|mixed $arguments
     * @return string
     * @throws BadArgumentException
     */
    public static function getModelClassNameFromArguments($arguments = [])
    {
        if (gettype($arguments) === 'string') {
            return $arguments;
        }
        if (gettype($arguments) === 'object') {
            return get_class($arguments);
        }

        if (gettype($arguments) === 'array') {
            if (empty($arguments)) {
                throw new BadArgumentException("Arguments field must contain at least 1 element");
            }

            if (gettype($arguments[0]) === 'string') {
                return $arguments[0];
            }
            if (gettype($arguments[0]) === 'object') {
                return get_class($arguments[0]);
            }
        }

        throw new BadArgumentException("Type for model must be a string or an object");
    }

    /**
     * @param string $ability
     * @param array|mixed $arguments
     * @return bool
     * @throws BadArgumentException
     */
    public static function allows($ability, $arguments = [])
    {
        if (self::shouldAuthorizeFor($ability, $arguments)) {
            return Gate::allows($ability, $arguments);
        }
        return true;
    }

    /**
     * Checks for item permissions, defined in Laravel Policies
     *
     * @param $item
     * @param RelationNode $rel_node
     * @param TraceableQuery $query
     * @throws BadArgumentException
     */
    public static function recursiveSetItemPermissions(&$item, RelationNode $rel_node, TraceableQuery &$query)
    {
        if ($item === null) {
            return;
        }

        $permission_name = $rel_node->getPermissionName();
        $relation_name = $rel_node->getName();
        $next = $rel_node->getNext();

        if ($permission_name === 'viewAny') {
            $item_class = get_class($item);

            $relation_class = get_class(
                with(new $item_class())->{$relation_name}()->getRelated()
            );

            if (self::isDropCandidate($relation_class)) {
                // just unset relation without warning, if we already passed it
                unset($item->{$relation_name});
            } else {
                if (!self::check('viewAny', $relation_class)) {
                    self::addToDropCandidates($relation_name);
                    $query->addWarning("You don't have access to see " . $relation_name);
                    unset($item->{$relation_name});
                }
            }
        }

        if ($permission_name === 'view') {
            if ($item->{$relation_name} != null) {
                $relation_class = get_class($item->{$relation_name});
                $relation_id = $item->{$relation_name}->id;

                if (self::isDropCandidate($relation_class . $relation_id)) {
                    unset($item->{$relation_name});
                } else {
                    if (!self::check('view', $item->{$relation_name})) {
                        self::addToDropCandidates($relation_class . $relation_id);
                        $query->addWarning("You don't have access to see " .
                            $relation_name .
                            " with id {$item->{$relation_name}->id} ");
                        unset($item->{$relation_name});
                    }
                }
            }
        }

        if ($next != null && $item->{$relation_name} != null) {
            if ($item->{$relation_name} instanceof Collection) {
                $item->{$relation_name}->map(function (&$nested_item) use (&$query, &$next) {

                    self::recursiveSetItemPermissions($nested_item, $next, $query);

                    return $nested_item;
                });
            } else {
                self::recursiveSetItemPermissions($item->{$relation_name}, $next, $query);
            }
        }
    }

    /**
     * @param string $item_key
     * @return bool
     */
    public static function isDropCandidate(string $item_key)
    {
        return in_array($item_key, self::$DROP_CANDIDATES);
    }

    /**
     * @param iterable|string $ability
     * @param array|mixed $arguments
     * @return bool
     * @throws BadArgumentException
     */
    public static function check($ability, $arguments = [])
    {
        if (self::shouldAuthorizeFor($ability, $arguments)) {
            return Gate::check($ability, $arguments);
        }
        return true;
    }

    /**
     * @param string $item_key
     */
    public static function addToDropCandidates(string $item_key)
    {
        self::$DROP_CANDIDATES[] = $item_key;
    }
}
