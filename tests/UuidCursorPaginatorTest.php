<?php

namespace Jamesh\UuidCursorPagination\Test;

use Jamesh\UuidCursorPagination\UuidCursorPaginator;

class UuidCursorPaginatorTest extends TestCase
{
    protected int $perPage = 3;

    public function test_create()
    {
        $paginator = new UuidCursorPaginator([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ], $this->perPage);

        $this->assertEquals($this->perPage, $paginator->perPage());
        $this->assertEquals($this->perPage, $paginator->count());
    }

    public function test_smallest_results(){
        $paginator = new UuidCursorPaginator($items = [
            ['id' => 1],
            ['id' => 2]
        ], $this->perPage);

        $this->assertEquals(count($items), $paginator->count());
    }

    public function test_overflow()
    {
        $paginator = new UuidCursorPaginator($items = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
            ['id' => 4],
            ['id' => 5]
        ], $this->perPage);

        $this->assertEquals([$items[0], $items[1], $items[2]], $paginator->items());
        $this->assertTrue($paginator->hasNext());
        $this->assertEquals(3, $paginator->nextCursor());
        $this->assertEquals($this->perPage, $paginator->count());
    }

    public function test_next_and_prev_cursors()
    {
        $paginator = new UuidCursorPaginator($items = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
            ['id' => 4]
        ], $this->perPage);

        $this->assertEquals(3, $paginator->nextCursor());
        $this->assertEquals(1, $paginator->previousCursor());
    }

    public function test_empty_next_when_no_more_results()
    {
        $paginator = new UuidCursorPaginator($items = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ], $this->perPage);

        $this->assertNull($paginator->nextCursor());
    }

}