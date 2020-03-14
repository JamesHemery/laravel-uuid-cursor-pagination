<?php

namespace Jamesh\UuidCursorPagination\Test\Fixtures;

abstract class PostData
{
    const TEST_1 = '60b68f45-545d-47b2-ac27-223c06e823c9';
    const TEST_2 = '958e9a4a-da4a-4431-9183-0974046b0645';
    const TEST_3 = '4761bc53-8126-46f8-9ba0-c9a029ff7418';
    const TEST_4 = '4f6029d7-61bf-4939-8b83-db8463c4db84';
    const TEST_5 = 'd905e0b0-f46a-4fa6-9546-85ee3208a202';
    const TEST_6 = 'eac7636b-df62-490d-878a-a6422e882982';
    const TEST_7 = '81fe6829-c625-43a6-bee5-db9823f138ff';
    const TEST_8 = '9ed13f7e-0420-4d86-ab9d-1a61103dc5c8';
    const TEST_9 = '563ce19e-53ed-4f4a-b939-926681af4487';
    const TEST_10 = 'ac5f8921-0243-4924-8a74-690568655fcb';

    public static function all()
    {
        return [
            ['id' => self::TEST_1, 'title' => 'Test 1'],
            ['id' => self::TEST_2, 'title' => 'Test 2'],
            ['id' => self::TEST_3, 'title' => 'Test 3'],
            ['id' => self::TEST_4, 'title' => 'Test 4'],
            ['id' => self::TEST_5, 'title' => 'Test 5'],
            ['id' => self::TEST_6, 'title' => 'Test 6'],
            ['id' => self::TEST_7, 'title' => 'Test 7'],
            ['id' => self::TEST_8, 'title' => 'Test 8'],
            ['id' => self::TEST_9, 'title' => 'Test 9'],
            ['id' => self::TEST_10, 'title' => 'Test 10']
        ];
    }

}