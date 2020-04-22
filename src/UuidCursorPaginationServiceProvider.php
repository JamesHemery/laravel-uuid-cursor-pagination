<?php

namespace Jamesh\UuidCursorPagination;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;

class UuidCursorPaginationServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMacro();
    }

    /**
     * Create Macros for the Builders.
     */
    public function registerMacro()
    {
        $macro = function (int $perPage = 10, array $columns = ['*'], array $options = []) {
            $options['request'] ??= request();
            $options['order_column'] ??= $this->model->getCreatedAtColumn();
            $options['order_direction'] ??= 'asc';
            
            $this->getQuery()->orders = null;

            $cursor = UuidCursorPaginator::resolveCursor($options['request']);
            $queryClone = clone $this;

            $getSortingValue = function ($value) use ($options) {
                return function ($query) use ($value, $options) {
                    $query->select($options['order_column'])
                        ->from($this->model->getTable())
                        ->where($this->model->getKeyName(), '=', $value);
                };
            };

            $hasElementBefore = function($query, $value) use ($options, $getSortingValue){
                $operator = $options['order_direction'] === 'asc' ? '<=' : '>=';
                $invertOrder = $options['order_direction'] === 'asc' ? 'desc' : 'asc';

                return $query
                    ->where($options['order_column'], $operator, $getSortingValue($value))
                    ->where('id', '!=', $value)
                    ->orderBy($options['order_column'], $invertOrder)
                    ->orderBy($query->model->getKeyName(), $invertOrder)
                    ->exists();
            };

            $hasElementAfter = function($query, $value) use ($options, $getSortingValue){
                $operator = $options['order_direction'] === 'asc' ? '>=' : '<=';
                return $this
                    ->where($options['order_column'], $operator, $getSortingValue($value))
                    ->where('id', '!=', $value)
                    ->orderBy($options['order_column'], $options['order_direction'])
                    ->orderBy($query->model->getKeyName(), $options['order_direction'])
                    ->exists();
            };

            if ($cursor->isBefore() && !$cursor->isAfter()) {
                $operator = $options['order_direction'] === 'asc' ? '<=' : '>=';
                $invertOrder = $options['order_direction'] === 'asc' ? 'desc' : 'asc';
                $results = $this
                    ->where($options['order_column'], $operator, $getSortingValue($cursor->getBeforeCursor()))
                    ->where('id', '!=', $cursor->getBeforeCursor())
                    ->orderBy($options['order_column'], $invertOrder)
                    ->orderBy($this->model->getKeyName(), $invertOrder)
                    ->take($perPage + 1)
                    ->get($columns)
                    ->reverse();

                if ($results->count() > $perPage){
                    $results->shift();
                    $hasPrevious = true;
                } else {
                    $hasPrevious = false;
                }

                $hasNext = $hasElementAfter($queryClone, $results->first()->id);

                return (new UuidCursorPaginator($results, $perPage, $options))
                    ->hasPrevious($hasPrevious)->hasNext($hasNext);
            } elseif($cursor->isBoth()) {
                $this
                    ->where($options['order_column'], '>=', $getSortingValue($cursor->getAfterCursor()))
                    ->where($options['order_column'], '<=', $getSortingValue($cursor->getBeforeCursor()))
                    ->whereNotIn('id', [$cursor->getAfterCursor(), $cursor->getBeforeCursor()]);

                $results = $this->orderBy($options['order_column'], $options['order_direction'])
                    ->orderBy($this->model->getKeyName(), $options['order_direction'])
                    ->take($perPage)
                    ->get($columns);

                $hasNext = true;
                $hasPrevious = true;
            }elseif($cursor->isAfter()){
                $operator = $options['order_direction'] === 'asc' ? '>=' : '<=';
                $this
                    ->where($options['order_column'], $operator, $getSortingValue($cursor->getAfterCursor()))
                    ->where('id', '!=', $cursor->getAfterCursor());
            }

            if (!isset($results)){
                $results = $this->orderBy($options['order_column'], $options['order_direction'])
                    ->orderBy($this->model->getKeyName(), $options['order_direction'])
                    ->take($perPage + 1)
                    ->get($columns);
            }

            if (!isset($hasNext)){
                $hasNext = $results->count() > $perPage;
            }

            if (!isset($hasPrevious)){
                $hasPrevious = $hasElementBefore($queryClone, $results->first()->id);
            }

            return (new UuidCursorPaginator($results, $perPage, $options))
                ->hasPrevious($hasPrevious)->hasNext($hasNext);
        };

        // Register macros
        QueryBuilder::macro('uuidCursorPaginate', $macro);
        EloquentBuilder::macro('uuidCursorPaginate', $macro);
    }
}
