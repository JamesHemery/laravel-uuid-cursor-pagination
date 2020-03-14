<?php

namespace Jamesh\UuidCursorPagination;

class Cursor
{

    protected $before;
    protected $after;

    public function __construct($before = null, $after = null)
    {
        $this->before = $before;
        $this->after = $after;
    }

    public function isPresent(): bool
    {
        return $this->isAfter() || $this->isBefore();
    }

    public function isBoth(): bool
    {
        return $this->isAfter() && $this->isBefore();
    }

    public function isAfter(): bool
    {
        return !is_null($this->after);
    }

    public function isBefore(): bool
    {
        return !is_null($this->before);
    }

    public function getBeforeCursor()
    {
        return $this->before;
    }

    public function getAfterCursor()
    {
        return $this->after;
    }

}