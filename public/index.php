<?php

require __DIR__ . '/../vendor/autoload.php';

use RoyallTheFourth\QuickList\Db;

session_start();
$config = RoyallTheFourth\QuickList\Common\config();
$db = Db\Common\connection();
$request = \Zend\Diactoros\ServerRequestFactory::fromGlobals();

$deps = [
    ':db' => $db,
    ':request' => $request,
    ':timezone' => new \DateTimeZone($config['default_timezone']),
    ':webPrefix' => $config['web_prefix']
];

$routes = RoyallTheFourth\QuickList\Route\common(
    $config['web_prefix'],
    Db\User\isValidUserId($db, $_SESSION['userId'] ?? -1)
);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($routes) {
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});

$dispatch = $dispatcher->dispatch($request->getMethod(), $request->getRequestTarget());

$disposition = $dispatch[0];
$handlerType = $dispatch[1] ?? null;
$vars = $dispatch[2] ?? [];

foreach ($vars as $name => $value) {
    $deps[":{$name}"] = $value;
}

switch ($disposition) {
    case FastRoute\Dispatcher::NOT_FOUND:
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $handlerType = RoyallTheFourth\QuickList\Action\NotFound\Get::class; // this fall-through is intentional
    case FastRoute\Dispatcher::FOUND:
        /** @var \RoyallTheFourth\QuickList\Action\ActionInterface $handler */
        (new \Zend\Diactoros\Response\SapiStreamEmitter())
            ->emit((new Auryn\Injector)
                ->make($handlerType, $deps)
                ->execute());
        break;
}
