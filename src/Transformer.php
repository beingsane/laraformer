<?php namespace KamranAhmed\Laraformer;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class Transformer
 *
 * Assists in transforming models or custom datasets
 *
 * @package KamranAhmed\Laraformer
 */
class Transformer
{
    public function __construct()
    {

    }

    /**
     * Transforms the classes having transform method
     *
     * @param $content
     * @return array
     */
    public function transformModel($content)
    {
        // In case of an array or collection
        if (is_array($content) || $content instanceof Collection) {
            $content = $this->transformObjects($content);
        } elseif (is_object($content) && $this->isTransformable($content)) {
            $content = $content->transform($content);
        } elseif ($content instanceof LengthAwarePaginator) {
            $meta         = $this->getPaginationMeta($content);
            $meta['data'] = $this->transformObjects($content->items());

            $content = $meta;
        }

        return $content;
    }

    /**
     * Transforms an array of objects using the objects transform method
     *
     * @param $toTransform
     * @return array
     */
    private function transformObjects($toTransform)
    {
        $transformed = [];
        foreach ($toTransform as $key => $item) {
            $transformed[$key] = $this->isTransformable($item) ? $item->transform($item) : $item;
        }

        return $transformed;
    }

    /**
     * Checks whether the object is transformable or not
     *
     * @param $item
     * @return bool
     */
    private function isTransformable($item)
    {
        return is_object($item) && method_exists($item, 'transform');
    }

    /**
     * Gets the pagination meta data. Assumes that a paginator
     * instance is passed \Illuminate\Pagination\LengthAwarePaginator
     *
     * @param $paginator
     * @return array
     */
    private function getPaginationMeta($paginator)
    {
        return [
            'total'          => $paginator->total(),
            'per_page'       => $paginator->perPage(),
            'current_page'   => $paginator->currentPage(),
            'last_page'      => $paginator->lastPage(),
            'next_page_url'  => $paginator->nextPageUrl(),
            'prev_page_url'  => $paginator->previousPageUrl(),
            'has_pages'      => $paginator->hasPages(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];
    }

    /**
     * To transform a custom dataset by providing the callback
     *
     * @param $content
     * @param $callback
     * @return array
     */
    public function forceTransform($content, $callback)
    {
        $transformedData = [];

        // If it is an iterateable content
        if (is_array($content) || $content instanceof Collection) {
            $transformedData = $this->callbackTransform($content, $callback);
        } else if (is_object($content)) { // In case of single object
            $transformedData = $callback($content);
        } else if ($content instanceof LengthAwarePaginator) { // In case it is paginated data
            $transformedData         = $this->getPaginationMeta($content);
            $transformedData['data'] = $this->callbackTransform($content, $callback);
        }

        return $transformedData;
    }

    /**
     * Calls the transformation callback on each item of the dataset
     *
     * @param $content
     * @param $callback
     * @return array
     */
    private function callbackTransform($content, $callback)
    {
        // If it is not a dataset and just a
        // single array. Need to improve
        if (empty($content[0])) {
            $content = [$content];
        }

        $transformedData = [];
        foreach ($content as $key => $item) {
            $transformedData[$key] = $callback($item);
        }

        return $transformedData;
    }
}
