<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        //se l'exception è di tipo ModelNotFoundException e la richiesta è fatta tramite api (quindi si aspetta un json come ritorno)
        if($exception instanceof ModelNotFoundException && $request->expectsJson()){
            //allora rispondi con un json che contiene un errore
            return response()->json(['errors' => true, 'message' => 'Model not found']);
        }else if($exception instanceof NotFoundHttpException && $request->expectsJson()){
            return response()->json(['errors' => true, 'message' => 'Endpoint not valid']);
        }
        return parent::render($request, $exception);
    }
}
