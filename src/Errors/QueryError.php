<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Shirokovnv\LaravelQueryApiBackend\Exceptions\UnknownErrorCategoryException;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Stringable;

abstract class QueryError implements JsonSerializable, Arrayable, Stringable
{
    public const AVAILABLE_CATEGORIES = [
        'validation',
        'authorization',
        'authentication',
        'client',
        'database',
        'server',
        'unknown'
    ];

    /**
     * @var string
     */
    protected $category;
    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @var bool
     */
    protected $traceable;

    public function __construct(Exception $e, string $category, bool $isTraceable = true)
    {
        $this->setException($e);
        $this->setTraceable($isTraceable);
        $this->setCategory($category);
    }

    /**
     * @return array
     */
    public function renderForClient()
    {
        return [
            'category' => $this->category,
            'content' => $this->getErrorContent()
        ];
    }

    public function renderForBackend()
    {
        return $this->renderForClient() +
        [
            'code' => $this->exception->getCode(),
            'file' => $this->exception->getFile(),
            'line' => $this->exception->getLine(),
            'trace' => $this->exception->getTrace()
        ];
    }

    public function toArray()
    {
        return $this->renderForClient();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    /**
     * @return array
     */
    abstract public function getErrorContent(): array;

    /**
     * @return array
     */
    public function getErrorTrace(): array
    {
        if ($this->isTraceable()) {
            return
                [
                    'file' => $this->exception->getFile(),
                    'line' => $this->exception->getLine(),
                    'trace' => $this->exception->getTrace()
                ];
        }

        return [];
    }

    /**
     * @return bool
     */
    public function isTraceable(): bool
    {
        return $this->traceable;
    }

    /**
     * @param bool $traceable
     */
    public function setTraceable(bool $traceable): void
    {
        $this->traceable = $traceable;
    }

    /**
     * @return string
     */
    protected function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @throws UnknownErrorCategoryException
     */
    protected function setCategory(string $category)
    {
        $this->checkCategory($category);
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    protected function getException()
    {
        return $this->exception;
    }

    /**
     * @param Exception $e
     */
    protected function setException(Exception $e)
    {
        $this->exception = $e;
    }

    /**
     * @param string $category
     * @throws UnknownErrorCategoryException
     */
    protected function checkCategory(string $category)
    {
        if (!in_array($category, self::AVAILABLE_CATEGORIES)) {
            throw new UnknownErrorCategoryException(self::AVAILABLE_CATEGORIES);
        }
    }
}
