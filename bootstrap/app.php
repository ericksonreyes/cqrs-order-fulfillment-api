<?php

declare(strict_types=1);

use Illuminate\Mail\MailServiceProvider;
use Nord\Lumen\Cors\CorsServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that php.sh before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \Nord\Lumen\Cors\CorsMiddleware::class,
    \App\Http\Middleware\Benchmark::class,
    \App\Http\Middleware\PreFlightResponse::class
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
$app->register(CorsServiceProvider::class);
$app->register(MailServiceProvider::class);

$app->bind(ContainerInterface::class, function () {

    /**
     * @param string $base
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    $recursive = function (string $base, string $pattern, int $flags = 0) use (&$recursive): array {
        if (substr($base, -1) !== DIRECTORY_SEPARATOR) {
            $base .= DIRECTORY_SEPARATOR;
        }

        $files = glob($base . $pattern, $flags);
        foreach (glob($base . '*', GLOB_ONLYDIR | GLOB_NOSORT | GLOB_MARK) as $dir) {
            $dirFiles = $recursive($dir, $pattern, $flags);
            if ($dirFiles !== false) {
                $files = array_merge($files, $dirFiles);
            }
        }

        return $files;
    };


    $containerBuilder = new ContainerBuilder();
    $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));

    $configurationFiles = $recursive(app()->basePath('config'), '*.yml');
    foreach ($configurationFiles as $configurationFile) {
        $loader->load($configurationFile);
    }

    return $containerBuilder;
});

$app->configure('mail');

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
