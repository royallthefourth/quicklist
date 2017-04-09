<?php

namespace RoyallTheFourth\QuickList\Db\MailingList;

use RoyallTheFourth\SmoothPdo\DataObject;

function add(DataObject $db, string $name): void
{
    $db->prepare('INSERT INTO lists(name) VALUES (?)')
        ->execute([$name]);
}

function addContactBulkSkipOptIn(DataObject $db, int $listId, iterable $emails): void
{
    $addContact = $db->prepare('INSERT OR IGNORE INTO contacts(email) VALUES(?)');
    $addToList = $db->prepare('INSERT OR IGNORE
INTO list_contacts(list_id, contact_id, date_optin, optin_hash)
VALUES(?,
(SELECT ROWID
FROM contacts
WHERE email = ?),
CURRENT_TIMESTAMP, \'skipped\');');

    foreach ($emails as $email) {
        $addContact->execute([$email]);
        $addToList->execute([$listId, $email]);
    }
}

function all(DataObject $db): iterable
{
    $stmt = $db->query('SELECT ROWID AS id, * FROM lists');
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function allByContact(DataObject $db, int $contactId): iterable
{
    $stmt = $db
        ->prepare('SELECT L.ROWID AS id, L.name, date_added, date_unsubscribed
        FROM lists L
        INNER JOIN list_contacts LC ON LC.contact_id = L.ROWID
        WHERE LC.contact_id = ?')
        ->execute([$contactId]);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function allContacts(DataObject $db, int $listId): iterable
{
    $stmt = $db->prepare('
SELECT
  LC.ROWID AS id,
  email,
  LC.date_added
FROM list_contacts LC
  INNER JOIN contacts C ON C.ROWID = LC.contact_id
WHERE LC.list_id = ?
      AND date_optin IS NOT NULL
      AND date_removed IS NULL
      AND date_unsubscribed IS NULL
      ');
    $stmt->execute([$listId]);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function getId(DataObject $db, string $name): int
{
    return $db->prepare('SELECT ROWID AS id
    FROM lists
    WHERE name = ?')
        ->execute([$name])
        ->fetch(\PDO::FETCH_ASSOC)['id'];
}

function getNameFromOptinHash(DataObject $db, string $hash): string
{
    return $db->prepare('SELECT name
FROM lists L
  INNER JOIN list_contacts LC ON LC.list_id = L.ROWID
WHERE LC.optin_hash = ?')
        ->execute([$hash])
        ->fetch(\PDO::FETCH_ASSOC)['name'];
}

function getNameFromUnsubHash(DataObject $db, string $hash): string
{
    return $db->prepare('SELECT name
FROM lists L
  INNER JOIN list_contacts LC ON LC.list_id = L.ROWID
  INNER JOIN deliveries D ON D.list_contact_id = LC.ROWID
WHERE D.unsub_hash = ?')
        ->execute([$hash])
        ->fetch(\PDO::FETCH_ASSOC)['name'];
}

function optInContact(DataObject $db, string $listName, string $email, string $hash): void
{
    $db->prepare('INSERT OR IGNORE INTO list_contacts(list_id, contact_id, optin_hash) VALUES(
  (SELECT ROWID FROM lists WHERE name = ?),
  (SELECT ROWID FROM contacts WHERE email = ?),
  ?)')
        ->execute([$listName, $email, $hash]);
}

function removeContact(DataObject $db, string $listName, string $email): void
{
    $db->prepare('UPDATE list_contacts
     SET date_removed = CURRENT_TIMESTAMP
  WHERE list_id = (SELECT ROWID FROM lists WHERE name = ?)
  AND contact_id = (SELECT ROWID FROM contacts WHERE email = ?)
  AND date_unsubscribed IS NOT NULL
  AND date_removed IS NULL
')
        ->execute([$listName, $email]);
}

function setOptin(DataObject $db, string $hash): void
{
    $db->prepare('UPDATE list_contacts
    SET date_optin = CURRENT_TIMESTAMP
    WHERE optin_hash = ?')
        ->execute([$hash]);
}

function setUnsub(DataObject $db, string $hash): void
{
    $db->prepare('UPDATE list_contacts
    SET date_unsubscribed = CURRENT_TIMESTAMP
    WHERE ROWID = (SELECT list_contact_id
    FROM deliveries WHERE unsub_hash = ?)')
        ->execute([$hash]);
}
