<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use JMS\Serializer\SerializerBuilder;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use ReflectionClass;
use Spatie\ArrayToXml\ArrayToXml;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        NotFoundHttpException::class
    ];

    /**
     * @var \Exception
     */
    private $exception;

    private $requestedContentType;

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if (env('APP_ENV') !== 'local' && $this->shouldReport($exception) && app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $this->requestedContentType = $request->header('Accept') === '*/*'
            ? 'application/json' : $request->header('Accept');
        if ((string)$this->requestedContentType === '') {
            $this->requestedContentType = 'application/json';
        }
        $this->exception = $exception;
        return parent::render($request, $exception);
    }

    /**
     * @param string $content
     * @param string $css
     * @return false|mixed|string
     * @throws \ReflectionException
     */
    public function decorate($content, $css)
    {
        $className = (new ReflectionClass($this->exception))->getShortName();

        $arrayContent = [
            '_error' => [
                'code' => $className,
                'message' => $this->exception->getMessage() !== '' ? $this->exception->getMessage() : $className
            ]
        ];
        if (env('APP_DEBUG')) {
            $arrayContent = [
                '_error' => [
                    'code' => $className,
                    'message' => $this->exception->getMessage() !== '' ? $this->exception->getMessage() : $className
                ]
            ];
        }
        $stringContent = json_encode($arrayContent);

        if (strpos($this->requestedContentType, 'yml') !== false) {
            $stringContent = self::makeYamlContent($arrayContent, 'yml');
        }
        if (strpos($this->requestedContentType, 'yaml') !== false) {
            $stringContent = self::makeYamlContent($arrayContent, 'yml');
        }
        if (strpos($this->requestedContentType, 'xml') !== false) {
            $stringContent = ArrayToXml::convert($arrayContent);
        }

        header('Content-type: ' . $this->requestedContentType);
        return $stringContent;
    }

    /**
     * @param array $arrayContent
     * @param $format
     * @return mixed
     */
    private function makeYamlContent(array $arrayContent, $format)
    {
        $serializer = SerializerBuilder::create()->build();
        $newContent = $serializer->serialize($arrayContent, $format);
        return $newContent;
    }
}
