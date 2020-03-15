<?php

namespace Jamesh\UuidCursorPagination\Test;

class RequestTest extends TestCase
{

    public function test_classic_json()
    {
        $response = $this->get('test-posts?test=1');

        $response->assertJsonFragment([
            'prev_page_url' => "api?test=1&before=60b68f45-545d-47b2-ac27-223c06e823c9",
            'next_page_url' => "api?test=1&after=958e9a4a-da4a-4431-9183-0974046b0645"
        ]);
    }

    public function test_collection()
    {
        $response = $this->get('test-posts-collection?test=1');

        $response->assertJsonFragment([
            'links' => [
                'first' => null,
                'last' => null,
                'next' => "api?test=1&after=958e9a4a-da4a-4431-9183-0974046b0645",
                'prev' => "api?test=1&before=60b68f45-545d-47b2-ac27-223c06e823c9"
            ]
        ]);
    }

}