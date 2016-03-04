<?php namespace KamranAhmed\Laraformer\Facades;

use Illuminate\Support\Facades\Facade;

class Transformer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'KamranAhmed\Laraformer\Transformer';
    }
}
