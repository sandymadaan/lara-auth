<?php

namespace App\Traits;

use Throwable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Rollbar\Rollbar;
use Rollbar\Payload\Level;
use Illuminate\Session\TokenMismatchException;

trait RestExceptionHandler
{
    protected $exception;

    protected function getJsonResponseForException(Throwable $exception)
    {
        $this->exception = $exception;

        switch (true) {
            case ($this->exception instanceof ValidationException):
                return $this->validation();

            case ($this->exception instanceof MethodNotAllowedHttpException):
                return $this->methodNotAllowedHttp();

            case ($this->exception instanceof NotFoundHttpException):
                return $this->notFoundHttp();

            case ($this->exception instanceof TokenMismatchException):
                return $this->tokenMismatch();

            case ($this->exception instanceof AuthenticationException):
                return $this->authenticationError();

            case ($this->exception instanceof AuthorizationException):
                return $this->authorizationError();

            default:
                return $this->serverError();
        }
    }

    protected function validation()
    {
        return $this->response(
            __('messages.error.validation'),
            400,
            $this->exception->validator->errors()->getMessages()
        );
    }

    protected function methodNotAllowedHttp()
    {
        return $this->response(__('messages.error.method_not_allowed'), 405, $this->exception->getMessage());
    }

    protected function notFoundHttp()
    {
        return $this->response(__('messages.error.not_found'), 404, $this->exception->getMessage());
    }

    protected function invalidHeaders()
    {
        return $this->response(__('messages.error.invalid_headers'), 400);
    }

    protected function invalidToken()
    {
        return $this->response(__('messages.error.invalid_token'), 401);
    }

    protected function response($message, $http_code, $data = '')
    {
        return new JsonResponse(
            ['message' => $message, 'data' => $data],
            $http_code
        );
    }

    protected function serverError()
    {
        $message = $this->exception->getMessage() . ' in ' .
        $this->exception->getFile() . ' at ' . $this->exception->getLine();
        Rollbar::log(Level::ERROR, $this->exception);
        Log::info('status=500 ' . $this->exception);
        return $this->response(__('messages.error.internal'), 500, $message);
    }

    protected function apiError()
    {
        return $this->response(
            $this->exception->getMessage(),
            $this->exception->getCode(),
            $this->exception->getData()
        );
    }

    protected function authenticationError()
    {
        return $this->response($this->exception->getMessage(), 401);
    }

    protected function authorizationError()
    {
        return $this->response(__('messages.error.unauthorized'), 403, $this->exception->getMessage());
    }

    protected function tokenMismatch()
    {
        return $this->response($this->exception->getMessage(), 419);
    }
}
