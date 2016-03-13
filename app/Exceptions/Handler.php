<?php namespace Owl\Exceptions;

/**
 * @copyright (c) owl
 */

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler
 *
 * @package Owl\Exceptions
 */
class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $routeName = $this->getRouteName($request->route());

        if ($e instanceof NotFoundHttpException) {
            // 記事ページを参照しようとしてエラーの場合、アイテムページ用404ページを表示
            if ($routeName === 'items.show') {
                return response()->view('errors.missing_item', [], 404);
            }

            return response()->view('errors.missing', [], 404);
        }

        // 本番環境の場合の処理
        if (app()->environment() === 'production') {
            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Get route name from route instance safety.
     *
     * @param mixed  $route
     *
     * @return string|null
     */
    protected function getRouteName($route)
    {
        if ($route instanceof \Illuminate\Routeing\Route) {
            return $route->getName();
        }

        return null;
    }
}
