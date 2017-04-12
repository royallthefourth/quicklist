<?php

namespace RoyallTheFourth\QuickList\Db\Contact;

use RoyallTheFourth\SmoothPdo\DataObject;

function add(\PDO $db, string $email): void
{
    $db->prepare('INSERT OR IGNORE INTO contacts(email) VALUES (?)')
        ->execute([$email]);
}

function addBulk(DataObject $db, iterable $emails): int
{
    $count = 0;
    $db->beginTransaction();
    $stmt = $db->prepare('INSERT OR IGNORE INTO contacts(email) VALUES(?)');
    foreach ($emails as $email) {
        $stmt->execute([$email]);
        $count++;
    }
    $db->commit();
    return $count;
}

function all(DataObject $db): iterable
{
    $stmt = $db->query('SELECT * FROM contacts');
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function count(DataObject $db): int
{
    return $db->query('SELECT COUNT(id) FROM contacts')->fetch(\PDO::FETCH_NUM)[0];
}

function oneById(DataObject $db, int $contactId): array
{
    return $db
        ->prepare('SELECT * FROM contacts WHERE id = ?')
        ->execute([$contactId])
        ->fetch(\PDO::FETCH_ASSOC);
}

function paginated(DataObject $db, int $page = 1, int $perPage = 50): iterable
{
    $stmt = $db->prepare('SELECT *
FROM contacts
ORDER BY date_added DESC
LIMIT ? OFFSET ?')
        ->execute([$perPage, ($page - 1) * $perPage]);

    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}
