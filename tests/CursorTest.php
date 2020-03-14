<?php

namespace Jamesh\UuidCursorPagination\Test;

use Illuminate\Http\Request;
use Jamesh\UuidCursorPagination\Cursor;
use Jamesh\UuidCursorPagination\Test\Fixtures\PostData;
use Jamesh\UuidCursorPagination\UuidCursorPaginator;

class CursorTest extends TestCase
{
    public function test_init()
    {
        $cursor = UuidCursorPaginator::resolveCursor();
        $this->assertInstanceOf(Cursor::class, $cursor);
    }

    public function test_resolves_default_cursor()
    {
        $req = new Request([]);
        $cursor = UuidCursorPaginator::resolveCursor($req);

        $this->assertFalse($cursor->isBefore());
        $this->assertFalse($cursor->isAfter());
        $this->assertFalse($cursor->isBoth());
        $this->assertFalse($cursor->isPresent());
    }

    public function test_resolves_after_cursor()
    {
        $req = new Request([
            'after' => PostData::TEST_2,
        ]);
        $cursor = UuidCursorPaginator::resolveCursor($req);

        $this->assertEquals(PostData::TEST_2, $cursor->getAfterCursor());

        $this->assertTrue($cursor->isPresent());
        $this->assertTrue($cursor->isAfter());

        $this->assertFalse($cursor->isBefore());
        $this->assertFalse($cursor->isBoth());
    }


    public function test_resolves_before_cursor()
    {
        $req = new Request([
            'before' => PostData::TEST_9,
        ]);
        $cursor = UuidCursorPaginator::resolveCursor($req);

        $this->assertEquals(PostData::TEST_9, $cursor->getBeforeCursor());

        $this->assertTrue($cursor->isPresent());
        $this->assertTrue($cursor->isBefore());

        $this->assertFalse($cursor->isAfter());
        $this->assertFalse($cursor->isBoth());
    }

    public function test_both_cursor()
    {
        $req = new Request([
            'after' => PostData::TEST_2,
            'before' => PostData::TEST_9
        ]);

        $cursor = UuidCursorPaginator::resolveCursor($req);

        $this->assertEquals(PostData::TEST_2, $cursor->getAfterCursor());
        $this->assertEquals(PostData::TEST_9, $cursor->getBeforeCursor());

        $this->assertTrue($cursor->isPresent());
        $this->assertTrue($cursor->isBefore());
        $this->assertTrue($cursor->isAfter());
        $this->assertTrue($cursor->isBoth());
    }

}