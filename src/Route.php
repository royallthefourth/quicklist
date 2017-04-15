<?php

namespace RoyallTheFourth\QuickList\Route;

use RoyallTheFourth\QuickList\Action;
use RoyallTheFourth\QuickList\Route\Base;
use RoyallTheFourth\QuickList\Route\Contact;
use RoyallTheFourth\QuickList\Route\Delivery;
use RoyallTheFourth\QuickList\Route\Message;
use RoyallTheFourth\QuickList\Route\MailingList;

function loggedIn(string $webPrefix): array
{
    return [
        ['GET', Base\home($webPrefix), Action\Dashboard\Get::class],
        ['GET', Base\logout($webPrefix), Action\Logout\Get::class],
        ['GET', Contact\index($webPrefix, '{page:\d+}'), Action\Contact\Index\Get::class],
        ['GET', Contact\view($webPrefix, '{contactId:\d+}'), Action\Contact\View\Get::class],
        ['GET', Message\index($webPrefix, '{page:\d+}'), Action\Message\Index\Get::class],
        ['GET', Message\view($webPrefix, '{messageId:\d+}'), Action\Message\View\Get::class],
        ['GET', Message\edit($webPrefix, '{messageId:\d+}'), Action\Message\Edit\Get::class],
        ['POST', Message\edit($webPrefix, '{messageId:\d+}'), Action\Message\Edit\Post::class],
        ['GET', Message\add($webPrefix), Action\Message\Add\Get::class],
        ['POST', Message\add($webPrefix), Action\Message\Add\Post::class],
        ['GET', MailingList\index($webPrefix, '{page:\d+}'), Action\MailingList\Index\Get::class],
        ['GET', MailingList\view($webPrefix, '{listId:\d+}'), Action\MailingList\View\Get::class],
        ['GET', MailingList\contacts($webPrefix, '{listId:\d+}', '{page:\d+}'), Action\MailingList\Contacts\Get::class],
        ['GET', MailingList\messages($webPrefix, '{listId:\d+}', '{page:\d+}'), Action\MailingList\Messages\Get::class],
        ['GET', MailingList\add($webPrefix), Action\MailingList\Add\Get::class],
        ['POST', MailingList\add($webPrefix), Action\MailingList\Add\Post::class],
        ['GET', Delivery\index($webPrefix, '{page:\d+}'), Action\Delivery\Index\Get::class]
    ];
}

function loggedOut(string $webPrefix): array
{
    return [
        ['GET', Base\home($webPrefix), Action\Login\Get::class],
        ['POST', Base\login($webPrefix), Action\Login\Post::class]
    ];
}

function common(string $webPrefix, bool $loggedIn): array
{
    $routes = [
        ['GET', Base\unsubscribeGet($webPrefix), Action\Unsubscribe\Get::class],
        ['POST', Base\unsubscribePost($webPrefix), Action\Unsubscribe\Post::class],
        ['GET', Base\optinGet($webPrefix), Action\Optin\Get::class],
        ['POST', Base\optinPost($webPrefix), Action\Optin\Post::class]
    ];

    if ($loggedIn) {
        $routes = array_merge($routes, loggedIn($webPrefix));
    } else {
        $routes = array_merge($routes, loggedOut($webPrefix));
    }

    return $routes;
}
