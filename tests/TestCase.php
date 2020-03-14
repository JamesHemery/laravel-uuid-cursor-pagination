<?php

namespace Jamesh\UuidCursorPagination\Test;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Jamesh\UuidCursorPagination\Test\Fixtures\Models\Post;
use Jamesh\UuidCursorPagination\Test\Fixtures\PostData;
use Jamesh\UuidCursorPagination\UuidCursorPaginationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setUpRoutes();
        $this->setUpDatabase();
    }

    protected function getPackageProviders($application): array
    {
        return [UuidCursorPaginationServiceProvider::class];
    }

    protected function setUpDatabase(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->timestamps();
        });

        Post::unguarded(function () {
            $data = PostData::all();
            $date = Carbon::now();
            foreach ($data as $post) {
                $post['created_at'] = $date;
                Post::create($post);
                $date = $date->clone()->addMinute();
            }
        });
    }

    protected function setUpRoutes(): void
    {
        Route::get('/test-posts', fn() => Post::uuidCursorPaginate(2, ['*'], ['path' => 'api'])->toJson());
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
