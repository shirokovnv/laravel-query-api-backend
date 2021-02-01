<?php


namespace Shirokovnv\LaravelQueryApiBackend\Support;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\BadArgumentException;

class RelationChain
{
    /**
     * @var RelationNode|null
     */
    private $root;

    public function __construct(string $model_class_name, array $relations)
    {
        $this->buildRelationSubChain($model_class_name, $relations);
    }

    /**
     * @param string $model_class_name
     * @param array $relations
     * @return RelationNode|null
     * @throws BadArgumentException
     */
    public function buildRelationSubChain(string $model_class_name, array $relations)
    {

        if (count($relations) === 0) {
            return null;
        }

        $relation_name = explode(":", $relations[0])[0];

        $related_model_class = get_class(
            with(new $model_class_name)->{$relation_name}()->getRelated()
        );

        $relation_class = get_class(
            with(new $model_class_name)->{$relation_name}()
        );

        $exploded_rel = explode("\\", $relation_class);

        $relation_kind = end($exploded_rel);

        $next = null;

        if (count($relations) > 1) {
            array_shift($relations);
            $next = $this->buildRelationSubChain($related_model_class, $relations);
        }

        $this->root = new RelationNode(
            $relation_kind,
            $relation_name,
            $this->getPermissionNameForRelation($relation_kind),
            $related_model_class,
            $next
        );

        return $this->root;
    }

    /**
     * @param string $relation_kind
     * @return string
     * @throws BadArgumentException
     */
    private function getPermissionNameForRelation(string $relation_kind)
    {

        $groupViewAny = [
            'BelongsToMany', 'HasMany', 'MorphMany', 'MorphToMany', 'HasManyThrough'
        ];

        $groupView = [
            'BelongsTo', 'HasOne', 'HasOneThrough', 'MorphTo', 'MorphOne'
        ];

        if (in_array($relation_kind, $groupView)) {
            return 'view';
        }
        if (in_array($relation_kind, $groupViewAny)) {
            return 'viewAny';
        }

        throw new BadArgumentException("Unknown relation $relation_kind");
    }

    /**
     * @return RelationNode|null
     */
    public function getRoot(): ?RelationNode
    {
        return $this->root;
    }

    /**
     * @param RelationNode|null $root
     */
    public function setRoot(?RelationNode $root): void
    {
        $this->root = $root;
    }
}
