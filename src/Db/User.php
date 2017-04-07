<?php

namespace RoyallTheFourth\QuickList\Db\User;

use RoyallTheFourth\SmoothPdo\DataObject;

function getByName(DataObject $db, string $name): array
{
    $user = $db->prepare('SELECT ROWID AS id, *
    FROM users
    WHERE name = ?')
        ->execute([$name])
        ->fetch(\PDO::FETCH_ASSOC);

    if ($user) {
        return $user;
    }
    return [];
}
