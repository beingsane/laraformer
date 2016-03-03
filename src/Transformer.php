<?php namespace KamranAhmed\LaraFormer;

class Transformer
{
    public function __construct()
    {

    }

    public function transformModel($modelData)
    {
        $transformed = [];
        foreach ($modelData as $key => $item) {
            if (is_object($item) && method_exists($item, 'transform')) {
                $transformed[$key] = $item->transform($item);
            } else {
                $transformed[$key] = $item;
            }
        }

        return $transformed;
    }
}
