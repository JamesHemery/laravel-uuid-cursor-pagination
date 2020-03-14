<?php

namespace Jamesh\UuidCursorPagination\Test;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Jamesh\UuidCursorPagination\Test\Fixtures\Models\Post;
use Jamesh\UuidCursorPagination\Test\Fixtures\PostData;
use Jamesh\UuidCursorPagination\UuidCursorPaginator;

class QueryMacroTest extends TestCase
{
    protected int $perPage = 4;

    public function test_output()
    {
        $results = Post::uuidCursorPaginate();
        $this->assertInstanceOf(UuidCursorPaginator::class, $results);
    }

    public function test_asc()
    {
        $results = Post::select('id')->uuidCursorPaginate($this->perPage);

        $this->assertEquals([
            PostData::TEST_1,
            PostData::TEST_2,
            PostData::TEST_3,
            PostData::TEST_4,
        ], Arr::pluck($results->items(), 'id'));

        $this->assertEquals($this->perPage, $results->count());
        $this->assertEquals(PostData::TEST_4, $results->nextCursor());

        $this->assertTrue($results->hasNext());
        $this->assertFalse($results->hasPrevious());
    }

    public function test_desc()
    {
        $results = Post::select('id')->uuidCursorPaginate($this->perPage, ['*'], [
            'order_direction' => 'desc'
        ]);

        $this->assertEquals([
            PostData::TEST_10,
            PostData::TEST_9,
            PostData::TEST_8,
            PostData::TEST_7
        ], Arr::pluck($results->items(), 'id'));

        $this->assertEquals(PostData::TEST_7, $results->nextCursor());
        $this->assertTrue($results->hasNext());
        $this->assertFalse($results->hasPrevious());
    }

    public function test_after_asc()
    {
        $results = Post::select('id')->uuidCursorPaginate($this->perPage, ['*'], [
            'request' => new Request([
                'after' => PostData::TEST_2
            ]),
        ]);

        $this->assertEquals([
            PostData::TEST_3,
            PostData::TEST_4,
            PostData::TEST_5,
            PostData::TEST_6
        ], Arr::pluck($results->items(), 'id'));

        $this->assertEquals(PostData::TEST_6, $results->nextCursor());
        $this->assertTrue($results->hasNext());
        $this->assertTrue($results->hasPrevious());
    }

    public function test_after_desc()
    {
        $results = Post::select('id')->uuidCursorPaginate($this->perPage, ['*'], [
            'order_direction' => 'desc',
            'request' => new Request([
                'after' => PostData::TEST_3
            ]),
        ]);

        $this->assertEquals([
            PostData::TEST_2,
            PostData::TEST_1
        ], Arr::pluck($results->items(), 'id'));

        $this->assertNull($results->nextCursor());
        $this->assertFalse($results->hasNext());
        $this->assertTrue($results->hasPrevious());
    }

    public function test_before_asc()
    {
        $results = Post::select('id')->uuidCursorPaginate($this->perPage, ['*'], [
            'request' => new Request([
                'before' => PostData::TEST_6
            ]),
        ]);

        $this->assertEquals([
            PostData::TEST_2,
            PostData::TEST_3,
            PostData::TEST_4,
            PostData::TEST_5
        ], Arr::pluck($results->items(), 'id'));

        $this->assertEquals(PostData::TEST_5, $results->nextCursor());
        $this->assertTrue($results->hasNext());
        $this->assertTrue($results->hasPrevious());
    }

    public function test_before_desc()
    {
        $results = Post::select('id')->uuidCursorPaginate($this->perPage, ['*'], [
            'order_direction' => 'desc',
            'request' => new Request([
                'before' => PostData::TEST_4
            ]),
        ]);

        $this->assertEquals([
            PostData::TEST_8,
            PostData::TEST_7,
            PostData::TEST_6,
            PostData::TEST_5
        ], Arr::pluck($results->items(), 'id'));

        $this->assertEquals(PostData::TEST_5, $results->nextCursor());
        $this->assertTrue($results->hasNext());
        $this->assertTrue($results->hasPrevious());
    }

    public function test_after_before_asc()
    {
        $results = Post::select('id')->uuidCursorPaginate(4, ['*'], [
            'request' => new Request([
                'after' => PostData::TEST_2,
                'before' => PostData::TEST_9
            ]),
        ]);

        $this->assertEquals([
            PostData::TEST_3,
            PostData::TEST_4,
            PostData::TEST_5,
            PostData::TEST_6,
        ], Arr::pluck($results->items(), 'id'));

        $this->assertEquals(PostData::TEST_6, $results->nextCursor());
        $this->assertTrue($results->hasNext());
        $this->assertTrue($results->hasPrevious());
    }

    public function test_after_before_desc()
    {
        $results = Post::select('id')->uuidCursorPaginate($this->perPage, ['*'], [
            'order_direction' => 'desc',
            'request' => new Request([
                'after' => PostData::TEST_2,
                'before' => PostData::TEST_9
            ]),
        ]);

        $this->assertEquals([
            PostData::TEST_8,
            PostData::TEST_7,
            PostData::TEST_6,
            PostData::TEST_5
        ], Arr::pluck($results->items(), 'id'));

        $this->assertEquals(PostData::TEST_5, $results->nextCursor());
        $this->assertTrue($results->hasNext());
        $this->assertTrue($results->hasPrevious());
    }

}