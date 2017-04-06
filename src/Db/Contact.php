<?php

namespace RoyallTheFourth\QuickList\Db\Contact;

use RoyallTheFourth\SmoothPdo\DataObject;

function add(\PDO $db, string $email): void
{
    $db->prepare('INSERT INTO contacts(email) VALUES (?)')
        ->execute([$email]);
}

function addBulk(DataObject $db, array $emails): void
{
    $stmt = $db->prepare('INSERT INTO contacts(email) VALUES(?)');
    foreach ($emails as $email) {
        $stmt->execute([$email]);
    }
}

function all(\PDO $db): array
{
    return $db->query('SELECT ROWID AS id, * FROM contacts')->fetchAll(\PDO::FETCH_ASSOC);
}
