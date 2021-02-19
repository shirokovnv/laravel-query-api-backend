<?php

namespace Shirokovnv\LaravelQueryApiBackend\Errors;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Shirokovnv\LaravelQueryApiBackend\Exceptions\UnknownErrorCategoryException;
use Stringable;

/**
 * Class QueryError.
 */
abstract class QueryError implements JsonSerializable, Arrayable, Stringable
{
    /**
     * Available error categories to show in frontend.
     */
    public const AVAILABLE_CATEGORIES = [
        'validation',
        'authorization',
        'authentication',
        'client',
        'database',
        'server',
        'unknown',
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

    /**
     * QueryError constructor.
     *
     * @param Exception $e
     * @param string $category
     * @param bool $isTraceable
     *
     * @throws UnknownErrorCategoryException
     */
    public function __construct(Exception $e, string $category, bool $isTraceable = true)
    {
        $this->setException($e);
        $this->setTraceable($isTraceable);
        $this->setCategory($category);
    }

    /**
     * @return array
     */
    public function renderForBackend(): array
    {
        return $this->renderForClient() +
            [
                'code' => $this->exception->getCode(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'trace' => $this->exception->getTrace(),
            ];
    }

    /**
     * @return array
     */
    public function renderForClient()
    {
        return [
            'category' => $this->category,
            'content' => $this->getErrorContent(),
        ];
    }

    /**
     * @return array
     */
    abstract public function getErrorContent(): array;

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->renderForClient();
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

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
                    'trace' => $this->exception->getTrace(),
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
    protected function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @throws UnknownErrorCategoryException
     */
    protected function setCategory(string $category): void
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
    protected function setException(Exception $e): void
    {
        $this->exception = $e;
    }

    /**
     * @param string $category
     *
     * @throws UnknownErrorCategoryException
     */
    protected function checkCategory(string $category): void
    {
        if (! in_array($category, self::AVAILABLE_CATEGORIES)) {
            throw new UnknownErrorCategoryException(self::AVAILABLE_CATEGORIES);
        }
    }
}
