<?php

namespace Shirokovnv\LaravelQueryApiBackend\Support;

class RelationNode
{
    /**
     * @var string
     */
    private $kind;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $permission_name;
    /**
     * @var string
     */
    private $related_class;
    /**
     * @var RelationNode | null
     */
    private $next;

    public function __construct(
        string $kind,
        string $name,
        string $permission_name,
        string $related_class,
        $next
    ) {
        $this->setKind($kind);
        $this->setName($name);
        $this->setPermissionName($permission_name);
        $this->setRelatedClass($related_class);
        $this->setNext($next);
    }

    /**
     * @return string
     */
    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * @param string $kind
     */
    public function setKind(string $kind): void
    {
        $this->kind = $kind;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPermissionName(): string
    {
        return $this->permission_name;
    }

    /**
     * @param string $permission_name
     */
    public function setPermissionName(string $permission_name): void
    {
        $this->permission_name = $permission_name;
    }

    /**
     * @return string
     */
    public function getRelatedClass(): string
    {
        return $this->related_class;
    }

    /**
     * @param string $related_class
     */
    public function setRelatedClass(string $related_class): void
    {
        $this->related_class = $related_class;
    }

    /**
     * @return RelationNode|null
     */
    public function getNext(): ?RelationNode
    {
        return $this->next;
    }

    /**
     * @param RelationNode|null $next
     */
    public function setNext(?RelationNode $next): void
    {
        $this->next = $next;
    }
}
