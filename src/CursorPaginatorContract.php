<?php


namespace Jamesh\UuidCursorPagination;

use Illuminate\Http\Request;

interface CursorPaginatorContract
{
    /**
     * Resolve cursor for a given request
     * @param Request|null $request
     * @return Cursor
     */
    public static function resolveCursor(?Request $request = null): Cursor;

    /**
     * Set paginated items
     * @param $items
     * @return $this
     */
    public function setItems($items);

    /**
     * Get the URL for items after a given cursor.
     *
     * @param string|int $cursor
     * @return string
     */
    public function urlAfter($cursor);

    /**
     * Get the URL for before a given cursor.
     *
     * @param string|int $cursor
     * @return string
     */
    public function urlBefore($cursor);

    /**
     * Add a set of query string values to the paginator.
     *
     * @param array|string $key
     * @param string|null $value
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function appends($key, $value = null);

    /**
     * Get / set the URL fragment to be appended to URLs.
     *
     * @param string|null $fragment
     * @return $this|string
     */
    public function fragment($fragment = null);

    /**
     * The URL for the next page, or null.
     *
     * @return string|null
     */
    public function nextPageUrl();

    /**
     * Get the URL for the previous page, or null.
     *
     * @return string|null
     */
    public function previousPageUrl();

    /**
     * Get the URL for the next cursor, or null.
     *
     * @return string|null
     */
    public function nextCursor();

    /**
     * Get the URL for the previous cursor, or null.
     *
     * @return string|null
     */
    public function previousCursor();

    /**
     * Get all of the items being paginated.
     *
     * @return array
     */
    public function items();

    /**
     * Get the "index" of the first item being paginated.
     *
     * @return int
     */
    public function firstItem();

    /**
     * Get the "index" of the last item being paginated.
     *
     * @return int
     */
    public function lastItem();

    /**
     * Determine how many items are being shown per page.
     *
     * @return int
     */
    public function perPage();

    /**
     * Determine the current cursor.
     *
     * @return int
     */
    public function currentCursor();

    /**
     * Determine if there is more items in the data store.
     *
     * @return bool
     */
    public function hasNext();

    /**
     * Determine if the list of items is empty or not.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Determine if the list of items is not empty.
     *
     * @return bool
     */
    public function isNotEmpty();

}