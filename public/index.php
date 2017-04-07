<?php

require __DIR__ . '/../vendor/autoload.php';

use RoyallTheFourth\QuickList\Db;

session_start();
$config = RoyallTheFourth\QuickList\Common\config();
$db = Db\Common\connection();
$request = \Zend\Diactoros\ServerRequestFactory::fromGlobals();

$deps = [
    ':db' => $db,
    ':webPrefix' => $config['web_prefix'],
    ':request' => $request
];

$routes = RoyallTheFourth\QuickList\Routes\common($config['web_prefix'], isset($_SESSION['userId']));

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($routes) {
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});

[$disposition, $handlerType, $vars] = $dispatcher->dispatch($request->getMethod(), $request->getRequestTarget());

foreach ($vars as $name => $value) {
    $deps[":{$name}"] = $value;
}

switch ($disposition) {
    case FastRoute\Dispatcher::NOT_FOUND:
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo '404';
        // TODO add 404 GET action
        break;
    case FastRoute\Dispatcher::FOUND:
        /** @var \RoyallTheFourth\QuickList\Action\ActionInterface $handler */
        (new \Zend\Diactoros\Response\SapiEmitter())
            ->emit((new Auryn\Injector)
                ->make($handlerType, $deps)
                ->execute());
        break;
}
