<?php

namespace Jamesh\UuidCursorPagination;

use ArrayAccess;
use Countable;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use IteratorAggregate;
use JsonSerializable;

class UuidCursorPaginator extends AbstractPaginator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Jsonable, PaginatorContract
{
    protected Request $request;
    protected Cursor $cursor;

    protected bool $hasNext = false;
    protected bool $hasPrevious = false;
    protected string $identifier = 'id';

    /**
     * Create a new paginator instance.
     *
     * @param mixed $items
     * @param int $perPage
     * @param array $options (path, query, fragment, pageName)
     * @return void
     */
    public function __construct($items, int $perPage, array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->perPage = $perPage;

        $this->request ??= request();
        $this->cursor = self::resolveCursor($this->request);

        $this->query = $this->getRawQuery();
        $this->path = $this->path !== '/' ? rtrim($this->path, '/') : rtrim($this->request->path(), '/');

        $this->setItems($items);
    }

    public function setItems($items)
    {
        $this->items = $items instanceof Collection ? $items : Collection::make($items);
        $this->hasNext = $this->items->count() > $this->perPage;
        $this->items = $this->items->slice(0, $this->perPage);

        return $this;
    }

    public function appends($key, $value = null)
    {
        if (is_array($key)) {
            $this->query = array_merge($this->query, $key);
        } else {
            $this->query[$key] = $value;
        }

        return $this;
    }

    public function fragment($fragment = null)
    {
        $this->fragment = $fragment;
    }

    protected function getRawQuery()
    {
        return collect($this->request->query())
            ->diffKeys([
                'after' => true,
                'before' => true
            ])->all();
    }

    public function url($cursor = [])
    {
        $query = array_merge($this->query, $cursor);

        return $this->path
            . (Str::contains($this->path, '?') ? '&' : '?')
            . http_build_query($query, '', '&')
            . ($this->fragment ? '#' . $this->fragment : '');
    }

    public function urlBefore($cursor)
    {
        return $this->url(['before' => $cursor]);
    }

    public function urlAfter($cursor)
    {
        return $this->url(['after' => $cursor]);
    }

    public function nextPageUrl()
    {
        if (!$this->nextCursor()) {
            return null;
        }

        return $this->urlAfter($this->nextCursor());
    }

    public function previousPageUrl()
    {
        if (!$this->previousCursor()) {
            return null;
        }

        return $this->urlBefore($this->previousCursor());
    }

    public function currentCursor()
    {
        return self::resolveCursor($this->request);
    }

    public static function resolveCursor(?Request $request = null): Cursor
    {
        $request ??= request();
        $prev = $request->get('before');
        $next = $request->get('after');

        return new Cursor($prev, $next);
    }

    public function hasNext(?bool $value = null)
    {
        if (is_null($value)) {
            return $this->hasNext;
        }

        $this->hasNext = $value;
        return $this;
    }

    public function hasPrevious(?bool $value = null)
    {
        if (is_null($value)) {
            return $this->hasPrevious;
        }

        $this->hasPrevious = $value;
        return $this;
    }

    public function hasMorePages()
    {
        return $this->hasNext();
    }

    public function nextCursor()
    {
        if (!$this->hasNext) {
            return null;
        }

        return $this->getCursorIdentifier($this->lastItem());
    }

    public function previousCursor()
    {
        return $this->getCursorIdentifier($this->firstItem());
    }

    protected function getCursorIdentifier($item)
    {
        if (!isset($item)) {
            return null;
        }

        if (is_array($item)) {
            return $item[$this->getCursorIdentifierName()];
        }

        return $item->{$this->getCursorIdentifierName()};
    }

    protected function getCursorIdentifierName(): string
    {
        return $this->identifier_alias ?? $this->identifier;
    }

    public function perPage()
    {
        return $this->perPage;
    }

    public function firstItem()
    {
        return $this->items->first();
    }

    public function lastItem()
    {
        return $this->items->last();
    }

    public function items()
    {
        return $this->items->all();
    }

    public function count()
    {
        return $this->items->count();
    }

    public function isEmpty()
    {
        return $this->items->isEmpty();
    }

    public function isNotEmpty()
    {
        return $this->items->isNotEmpty();
    }

    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'path' => $this->url(),
            'previous_cursor' => $this->previousCursor(),
            'next_cursor' => $this->nextCursor(),
            'per_page' => (int)$this->perPage(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function render($view = null, $data = [])
    {
        return '';
    }
}