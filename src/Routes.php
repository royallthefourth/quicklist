<?php

namespace RoyallTheFourth\QuickList\Routes;

use RoyallTheFourth\QuickList\Action;

function loggedIn(string $webPrefix): array
{
    return [
        ['GET', "{$webPrefix}/", Action\Dashboard\Get::class],
        ['GET', "{$webPrefix}/logout", Action\Logout\Get::class]
    ];
}

function loggedOut(string $webPrefix): array
{
    return [
        ['GET', "{$webPrefix}/", Action\Login\Get::class],
        ['POST', "{$webPrefix}/login", Action\Login\Post::class]
    ];
}

function common(string $webPrefix, bool $loggedIn): array
{
    $routes = [
        ['GET', "{$webPrefix}/unsubscribe/{hash}", Action\Unsubscribe\Get::class],
        ['POST', "{$webPrefix}/unsubscribe", Action\Unsubscribe\Post::class],
        ['GET', "{$webPrefix}/optin/{hash}", Action\Optin\Get::class],
        ['POST', "{$webPrefix}/optin", Action\Optin\Post::class]
    ];

    if ($loggedIn) {
        $routes = array_merge($routes, loggedIn($webPrefix));
    } else {
        $routes = array_merge($routes, loggedOut($webPrefix));
    }

    return $routes;
}
