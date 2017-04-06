<?php

namespace RoyallTheFourth\QuickList\Db\MailingList;

use RoyallTheFourth\SmoothPdo\DataObject;

function all(DataObject $db): array
{
    if (!($rs = $db->query('SELECT ROWID AS id, * FROM lists')->fetch(\PDO::FETCH_ASSOC))) {
        $rs = [];
    }

    return $rs;
}

function allContacts(DataObject $db, string $listId): \Generator
{
    $stmt = $db->prepare('
SELECT
  LC.ROWID AS id,
  email,
  LC.date_added
FROM list_contacts LC
  INNER JOIN contacts C ON C.ROWID = LC.contact_id
WHERE LC.ROWID = ?
      AND date_optin IS NOT NULL
      AND date_removed IS NULL
      AND date_unsubscribed IS NULL
      ');
    $stmt->execute([$listId]);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function add(DataObject $db, string $name): void
{
    $db->prepare('INSERT INTO lists(name) VALUES (?)')
        ->execute([$name]);
}

function addContact(DataObject $db, string $listName, string $email, string $hash): void
{
    $db->prepare('INSERT OR IGNORE INTO list_contacts(list_id, contact_id, optin_hash) VALUES(
  (SELECT ROWID FROM lists WHERE name = ?),
  (SELECT ROWID FROM contacts WHERE email = ?),
  ?)')
        ->execute([$listName, $email, $hash]);
}

function getNameFromHash(DataObject $db, string $hash): string
{
    return $db->prepare('SELECT name
FROM lists L
  INNER JOIN list_contacts LC ON LC.list_id = L.ROWID
WHERE LC.optin_hash = ?')
        ->execute([$hash])
        ->fetch(\PDO::FETCH_ASSOC)['name'];
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
