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

    public function transformObjects($toTransform)
    {
        $transformed = [];
        foreach ($toTransform as $key => $item) {
            $transformed[$key] = $this->isTransformable($item) ? $item->transform($item) : $item;
        }

        return $transformed;
    }

    public function isTransformable($item)
    {
        return is_object($item) && method_exists($item, 'transform');
    }

    public function getPaginationMeta($paginator)
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
}
