<?php

namespace RoyallTheFourth\QuickList\MailingList;

use RoyallTheFourth\SmoothPdo\DataObject;

function optIn(DataObject $db, string $listName, string $email, string $domain): void
{
    $hash = sha1($listName . $email . time());
    \RoyallTheFourth\QuickList\Db\MailingList\optInContact($db, $listName, $email, $hash);

    $body = <<<body
You've signed up for {$listName}.
To confirm your subscription, please click this link:
https://{$domain}/optin/{$hash}

If you did not sign up to receive emails from this list, please disregard this message.
body;

    \RoyallTheFourth\QuickList\Db\Delivery\add(
        $db,
        \RoyallTheFourth\QuickList\Db\Message\add($db, "{$listName} Confirmation", $body),
        $listName,
        $email,
        new \DateTimeImmutable(),
        ''
    );
}
