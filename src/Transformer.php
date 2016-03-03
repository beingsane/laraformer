<?php namespace KamranAhmed\LaraFormer;

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
}
