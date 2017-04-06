<?php

namespace RoyallTheFourth\QuickList\Routes;

use RoyallTheFourth\QuickList\Action;

function loggedIn(string $webPrefix): array
{
    // TODO / points to dashboard
    // TODO logout page
    return [];
}

function loggedOut(string $webPrefix): array
{
    // TODO / points to login page
    // merge this function's output with common() and return
    return [];
}

function common(string $webPrefix): array
{
    return [
        ['GET', "{$webPrefix}/unsubscribe/{hash}", Action\Unsubscribe\Get::class],
        ['POST', "{$webPrefix}/unsubscribe", Action\Unsubscribe\Post::class],
        ['GET', "{$webPrefix}/optin/{hash}", Action\Optin\Get::class],
        ['POST', "{$webPrefix}/optin", Action\Optin\Post::class]
    ];
}
