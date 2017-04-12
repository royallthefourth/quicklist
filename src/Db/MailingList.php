<?php

namespace RoyallTheFourth\QuickList\Db\MailingList;

use function RoyallTheFourth\QuickList\Delivery\messageHash;
use function RoyallTheFourth\QuickList\MailingList\optInBody;
use RoyallTheFourth\SmoothPdo\DataObject;

function add(DataObject $db, string $name): void
{
    $db->prepare('INSERT INTO lists(name) VALUES (?)')
        ->execute([$name]);
}

function addContactBulkSkipOptIn(DataObject $db, int $listId, iterable $emails): void
{
    $db->beginTransaction();
    $addContact = $db->prepare('INSERT OR IGNORE INTO contacts(email) VALUES(?)');
    $addToList = $db->prepare('INSERT OR IGNORE
INTO list_contacts(list_id, contact_id, date_optin, optin_hash)
VALUES(?,
(SELECT id
FROM contacts
WHERE email = ?),
CURRENT_TIMESTAMP, \'skipped\');');

    foreach ($emails as $email) {
        $addContact->execute([$email]);
        $addToList->execute([$listId, $email]);
    }
    $db->commit();
}

function all(DataObject $db): iterable
{
    $stmt = $db->query('SELECT * FROM lists');
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

function allByContact(DataObject $db, int $contactId): iterable
{
    $stmt = $db
        ->prepare('SELECT L.id, L.name, date_added, date_unsubscribed
        FROM lists L
        INNER JOIN list_contacts LC ON LC.contact_id = L.id
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

function bulkOptIn(DataObject $db, iterable $emails, int $listId, string $domain): void
{
    $listName = $db
        ->prepare('SELECT name FROM lists WHERE id = ?')
        ->execute([$listId])->fetch(\PDO::FETCH_NUM)[0];
    $insertEmail = $db->prepare('INSERT OR IGNORE INTO contacts(email) VALUES(?)');
    $insertListContact = $db
        ->prepare('INSERT OR IGNORE INTO list_contacts(list_id, contact_id, optin_hash)
VALUES(?,
(SELECT id FROM contacts WHERE email = ?),
?)');
    $getListContactId = $db
        ->prepare('SELECT LC.id FROM list_contacts LC
INNER JOIN contacts C ON C.ROWID = LC.contact_id
WHERE C.email = ? AND LC.list_id = ?
 ORDER BY LC.date_added DESC LIMIT 1');
    $insertMessage = $db->prepare('INSERT OR IGNORE INTO messages(subject, body, list_contact_id) VALUES(?, ?, ?)');
    $getMessageId = $db
        ->prepare('SELECT id FROM messages WHERE list_contact_id = ? ORDER BY date_added DESC LIMIT 1');
    $insertDelivery = $db
        ->prepare('INSERT OR IGNORE INTO deliveries(message_id, list_contact_id, date_scheduled)
VALUES(?, ?, CURRENT_TIMESTAMP)');

    $db->beginTransaction();

    foreach ($emails as $email) {
            $hash = messageHash($listId . $email);
            $insertEmail->execute([$email]);
            $insertListContact->execute([$listId, $email, $hash]);
            $listContactId = $getListContactId->execute([$email, $listId])->fetch(\PDO::FETCH_NUM)[0];
            $insertMessage->execute(["{$listName} Confirmation", optInBody($listName, $domain, $hash), $listContactId]);
            $messageId = $getMessageId->execute([$listContactId])->fetch(\PDO::FETCH_NUM)[0];
            $insertDelivery->execute([$messageId, $listContactId]);
    }
    $db->commit();
}

function count(DataObject $db): int
{
    return $db->query('SELECT COUNT(id) FROM lists')->fetch(\PDO::FETCH_NUM)[0];
}

function getId(DataObject $db, string $name): int
{
    return $db->prepare('SELECT id
    FROM lists
    WHERE name = ?')
        ->execute([$name])
        ->fetch(\PDO::FETCH_ASSOC)['id'];
}

function getNameFromOptinHash(DataObject $db, string $hash): string
{
    return $db->prepare('SELECT name
FROM lists L
  INNER JOIN list_contacts LC ON LC.list_id = L.id
WHERE LC.optin_hash = ?')
        ->execute([$hash])
        ->fetch(\PDO::FETCH_ASSOC)['name'];
}

function getNameFromUnsubHash(DataObject $db, string $hash): string
{
    return $db->prepare('SELECT name
FROM lists L
  INNER JOIN list_contacts LC ON LC.list_id = L.id
  INNER JOIN deliveries D ON D.list_contact_id = LC.id
WHERE D.unsub_hash = ?')
        ->execute([$hash])
        ->fetch(\PDO::FETCH_ASSOC)['name'];
}

function optInContact(DataObject $db, string $listName, string $email, string $hash): void
{
    $db->prepare('INSERT OR IGNORE INTO list_contacts(list_id, contact_id, optin_hash) VALUES(
  (SELECT id FROM lists WHERE name = ?),
  (SELECT id FROM contacts WHERE email = ?),
  ?)')
        ->execute([$listName, $email, $hash]);
}

function removeContact(DataObject $db, string $listName, string $email): void
{
    $db->prepare('UPDATE list_contacts
     SET date_removed = CURRENT_TIMESTAMP
  WHERE list_id = (SELECT id FROM lists WHERE name = ?)
  AND contact_id = (SELECT id FROM contacts WHERE email = ?)
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
    WHERE id = (SELECT list_contact_id
    FROM deliveries WHERE unsub_hash = ?)')
        ->execute([$hash]);
}

function summary(DataObject $db, int $page = 1, int $perPage = 10): iterable
{
    $stmt = $db
        ->prepare('SELECT
        L.id AS id, L.name, COUNT(LC.id) AS contacts, COUNT(M.id) AS messages, COUNT(D.id) AS deliveries
FROM lists L
LEFT JOIN list_contacts LC ON LC.list_id = L.id
LEFT JOIN deliveries D ON D.list_contact_id = LC.id
LEFT JOIN messages M ON M.id = D.message_id
GROUP BY L.id, L.name
ORDER BY L.name ASC
LIMIT ? OFFSET ?')
        ->execute([$perPage, ($page - 1) * $perPage]);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        yield $row;
    }
}
