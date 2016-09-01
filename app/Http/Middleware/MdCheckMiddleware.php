<?php namespace App\Http\Middleware;
use Closure;

class MdCheckMiddleware {
    /**
     * Handle an incoming request.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $item_id) {
        if(preg_match('/.+\.md/', $item_id)) {
            return redirect()->action('ItemsController@showMd', $item_id);
        }

        return $next($request);
    }
}
