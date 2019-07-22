<?php

namespace App\Http\Controllers;

use EricksonReyes\DomainDrivenDesign\Common\Exception\AuthenticationFailureException;
use EricksonReyes\DomainDrivenDesign\Common\Exception\DeletedRecordException;
use EricksonReyes\DomainDrivenDesign\Common\Exception\PermissionDeniedException;
use EricksonReyes\DomainDrivenDesign\Common\Exception\RecordConflictException;
use EricksonReyes\DomainDrivenDesign\Common\Exception\RecordNotFoundException;
use EricksonReyes\DomainDrivenDesign\Infrastructure\CommandBus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use JMS\Serializer\SerializerBuilder;
use Laravel\Lumen\Routing\Controller as BaseController;
use ReflectionClass;
use Spatie\ArrayToXml\ArrayToXml;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
abstract class Controller extends BaseController
{
    /**
     * @var array
     */
    private static $exceptionMap = [
        AuthenticationFailureException::class => 401,
        PermissionDeniedException::class => 403,
        RecordNotFoundException::class => 404,
        DeletedRecordException::class => 410,
        RecordConflictException::class => 409,
        InvalidArgumentException::class => 400,
    ];


    /**
     * @var string
     */
    protected $currentUserId;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Request
     */
    private $request;

    /**
     * Controller constructor.
     * @param ContainerInterface $container
     * @param Request $request
     */
    public function __construct(ContainerInterface $container, Request $request)
    {
        $this->container = $container;
        $this->request = $request;

        if ($request->has('token')) {
            $this->currentUserId = $request->get('token')['sub'];
        }
    }

    /**
     * @return string
     */
    public function currentlyLoggedInUser(): string
    {
        return $this->currentUserId;
    }

    /**
     * @param Exception $exception
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    protected function exception(Exception $exception)
    {
        $code = $this->isExceptionWithinHttpStatusCodeRange($exception) ?
            $exception->getCode() :
            (new ReflectionClass($exception))->getShortName();
        $httpCode = $this->isExceptionWithinHttpStatusCodeRange($exception) ? $exception->getCode() : 500;

        foreach (self::$exceptionMap as $exceptionClassName => $storedHttpCode) {
            if ($exception instanceof $exceptionClassName) {
                $httpCode = $storedHttpCode;
                break;
            }
        }

        if (env('APP_DEBUG')) {
            return $this->response(
                [
                    '_error' => [
                        'code' => $code,
                        'message' => $exception->getMessage(),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                    ]
                ],
                $httpCode
            );
        }

        return $this->response(
            [
                '_error' => [
                    'code' => $code,
                    'message' => $exception->getMessage()
                ]
            ],
            $httpCode
        );
    }

    /**
     * @param Exception $exception
     * @return bool
     */
    private function isExceptionWithinHttpStatusCodeRange(\Exception $exception): bool
    {
        return $exception->getCode() >= 200 && $exception->getCode() < 600;
    }

    /**
     * @param $response
     * @param int $responseCode
     * @param array $headers
     * @return Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function response($response, $responseCode = 200, $headers = [])
    {
        if (is_array($response)) {
            $response = array_map(function ($value) {
                return $value ?? '';
            }, $response);
        }

        $expectedContentType = 'application/json';
        $responseObject = \response($response, $responseCode, $headers);
        $requestedContentType = $this->request->header('Accept') ?? $expectedContentType;
        $stringContent = $responseObject->getContent();

        $arrayContent = json_decode($responseObject->content(), true);
        if ($arrayContent && $requestedContentType !== '*/*') {
            $expectedContentType = $requestedContentType;
            if (strpos($requestedContentType, 'yml') !== false) {
                $stringContent = $this->makeYamlContent($responseObject, 'yml');
            }
            if (strpos($requestedContentType, 'yaml') !== false) {
                $stringContent = $this->makeYamlContent($responseObject, 'yml');
            }
            if (strpos($requestedContentType, 'xml') !== false) {
                $stringContent = ArrayToXml::convert($arrayContent);
            }
        }

        return $responseObject->setContent($stringContent)->header('Content-type', $expectedContentType);
    }

    /**
     * @param Response $response
     * @param $format
     * @return mixed|string
     */
    private function makeYamlContent(Response $response, $format)
    {
        $serializer = SerializerBuilder::create()->build();
        $newContent = $serializer->serialize(json_decode($response->content(), true), $format);
        return $newContent;
    }

    /**
     * @return CommandBus|null
     */
    protected function handler(): ?CommandBus
    {
        return $this->container()->get('command_bus');
    }

    /**
     * @return ContainerInterface
     */
    protected function container(): ContainerInterface
    {
        return $this->container;
    }
}
