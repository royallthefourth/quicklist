<?php

use Phinx\Migration\AbstractMigration;

class InitialSetup extends AbstractMigration
{
    public function up()
    {
        $this->execute('CREATE TABLE users(
            `name` TEXT UNIQUE COLLATE NOCASE,
            `password` TEXT
          );
          
          CREATE TABLE lists(
            `name` TEXT UNIQUE COLLATE NOCASE
          );
          
          CREATE TABLE contacts(
            email TEXT UNIQUE COLLATE NOCASE,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP
          );
          
          CREATE TABLE list_contacts(
            list_id INTEGER REFERENCES lists(ROWID),
            contact_id INTEGER REFERENCES contacts(ROWID),
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_removed DATETIME,
            date_optin DATETIME,
            date_unsubscribed DATETIME,
            optin_hash TEXT
          );
          
          CREATE TABLE messages(
            subject TEXT,
            body TEXT,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP
          );
          
          CREATE TABLE deliveries(
            message_id INTEGER REFERENCES messages(ROWID),
            list_contact_id INTEGER REFERENCES list_contacts(ROWID),
            date_scheduled DATETIME NOT NULL,
            date_sent DATETIME,
            date_canceled DATETIME,
            unsub_hash TEXT
          );
          
          CREATE UNIQUE INDEX uq_list_contacts ON list_contacts(list_id, contact_id);
          CREATE UNIQUE INDEX uq_deliveries ON deliveries(message_id, list_contact_id);
          PRAGMA journal_mode=WAL;');
    }
}
