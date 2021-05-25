<?php
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UploadedFileFactory;
use Spiral\RoadRunner;
use Symfony\Component\Dotenv\Dotenv;
use function DI\create;

require __DIR__ . '/vendor/autoload.php';

(new Dotenv())->loadEnv(__DIR__.'/.env');

$builder = new ContainerBuilder();
$builder->addDefinitions([
    ServerRequestFactoryInterface::class => create(ServerRequestFactory::class),
    StreamFactoryInterface::class => create(StreamFactory::class),
    UploadedFileFactoryInterface::class => create(UploadedFileFactory::class),
    // Foo::class => create(),
]);
$container = $builder->build();

$app = Bridge::create($container);
$app->get('/', function(Request $request, Response $response) {
    $response = $response->withHeader('Content-Type', 'text/plain');
    $response->getBody()->write("Hello World\n");
    return $response;
});
$app->addErrorMiddleware($_ENV['APP_ENV'] === 'dev', true, true);

$rrWorker = RoadRunner\Worker::create();
$psrWorker = new RoadRunner\Http\PSR7Worker(
    $rrWorker,
    $container->get(ServerRequestFactoryInterface::class),
    $container->get(StreamFactoryInterface::class),
    $container->get(UploadedFileFactoryInterface::class)
);
while ($req = $psrWorker->waitRequest()) {
    try {
        $psrWorker->respond($app->handle($req));
    } catch (Throwable $e) {
        $psrWorker->getWorker()->error((string)$e);
    }
}
