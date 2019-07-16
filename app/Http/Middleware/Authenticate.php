<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Authenticate constructor.
     * @param Auth $auth
     * @param ContainerInterface $container
     */
    public function __construct(Auth $auth, ContainerInterface $container)
    {
        $this->auth = $auth;
        $this->container = $container;
    }

    private function container(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory|mixed
     * @throws \ReflectionException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (isset($_SERVER['USER_ACCOUNT'])) {
            $request->merge(['user_account' => $_SERVER['USER_ACCOUNT']]);
        }

        if ($request->hasHeader('Authorization')) {
            [$token] = sscanf($request->header('Authorization'), 'Bearer %s');
            if ($token) {
                $decryptionSecretKey = $this->container()->getParameter('jwt_secret_key');
                if ($this->container()->getParameter('jwt_algorithm') === 'RS256') {
                    $decryptionSecretKey = file_get_contents(
                        $this->container()->getParameter('jwt_decryption_key_file')
                    );
                }

                try {
                    $tokenArray = JWT::decode(
                        $token,
                        $decryptionSecretKey,
                        [$this->container()->getParameter('jwt_algorithm')]
                    );
                } catch (Exception $exception) {
                    return response(
                        [
                            '_error' => [
                                'code' => (new ReflectionClass($exception))->getShortName(),
                                'message' => $exception->getMessage()
                            ]
                        ],
                        401
                    );
                }

                return $next($request->merge(['token' => (array)$tokenArray]));
            }
        }

        return response(
            [
                'code' => 'MissingAuthorizationHeader',
                'message' => 'Missing Authorization Header'
            ],
            401
        );
    }
}
