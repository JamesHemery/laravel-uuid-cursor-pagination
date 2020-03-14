<?php

namespace Jamesh\UuidCursorPagination\Test\Fixtures\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Jamesh\UuidCursorPagination\UuidCursorPaginator;

/**
 * Class Post
 * @package Jamesh\UuidCursorPagination\Test\Fixtures\Models
 */
class Post extends Model
{
    protected $guarded = [];

    public function getKeyType()
    {
        return 'string';
    }

    public function getIncrementing()
    {
        return false;
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function (Model $model) {
            $model->keyType = 'string';
            $model->incrementing = false;

            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string)Str::uuid();
            }
        });
    }

}
