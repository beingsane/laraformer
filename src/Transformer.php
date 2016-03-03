<?php namespace KamranAhmed\LaraFormer;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Transformer
{
    public function __construct()
    {

    }

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

    private function transformObjects($toTransform)
    {
        $transformed = [];
        foreach ($toTransform as $key => $item) {
            $transformed[$key] = $this->isTransformable($item) ? $item->transform($item) : $item;
        }

        return $transformed;
    }

    private function isTransformable($item)
    {
        return is_object($item) && method_exists($item, 'transform');
    }

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

    private function callbackTransform($content, $callback)
    {
        $transformedData = [];

        foreach ($content as $key => $item) {
            $transformedData[$key] = $callback($item);
        }

        return $transformedData;
    }
}
