<?php

namespace RoyallTheFourth\QuickList\MailingList;

use RoyallTheFourth\QuickList\Db\MailingList;
use function RoyallTheFourth\QuickList\Delivery\messageHash;
use RoyallTheFourth\SmoothPdo\DataObject;

function optIn(DataObject $db, string $listName, string $email, string $domain): void
{
    $hash = messageHash($email.$listName);
    \RoyallTheFourth\QuickList\Db\MailingList\optInContact($db, $listName, $email, $hash);

    $body = optInBody($listName, $domain, $hash);

    \RoyallTheFourth\QuickList\Db\Delivery\add(
        $db,
        \RoyallTheFourth\QuickList\Db\Message\add($db, "{$listName} Confirmation", $body),
        MailingList\getId($db, $listName),
        $email,
        new \DateTimeImmutable(),
        ''
    );
}

function optInBody(string $listName, string $domain, string $hash): string
{
    return <<<body
You've signed up for {$listName}.
To confirm your subscription, please click this link:
https://{$domain}/optin/{$hash}

If you did not sign up to receive emails from this list, please disregard this message.
body;
}
