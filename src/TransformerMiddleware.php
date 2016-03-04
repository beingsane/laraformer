<?php namespace KamranAhmed\LaraFormer;

use Closure;

/**
 * Class TransformerMiddleware
 *
 * Middleware that tries to automatically transform the data provided
 * that is returned in the form of direct model
 */
class TransformerMiddleware
{
    public function __construct(Transformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Having the `original` property means that we have the models and
        // the response can be tried to transform
        if (property_exists($response, 'original')) {
            // Transform based on model and reset the content
            $content = $this->transformer->transformModel($response->original);
            $response->setContent($content);
        }

        return $response;
    }
}

