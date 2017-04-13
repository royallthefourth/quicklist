<?php

use Phinx\Migration\AbstractMigration;

class InitialSetup extends AbstractMigration
{
    public function up()
    {
        $this->execute('CREATE TABLE users(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            `name` TEXT UNIQUE COLLATE NOCASE,
            `password` TEXT
          );
          
          CREATE TABLE lists(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            `name` TEXT UNIQUE COLLATE NOCASE
          );
          
          CREATE TABLE contacts(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE COLLATE NOCASE,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP
          );
          
          CREATE TABLE list_contacts(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            list_id INTEGER REFERENCES lists(id),
            contact_id INTEGER REFERENCES contacts(id),
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_removed DATETIME,
            date_optin DATETIME,
            date_unsubscribed DATETIME,
            optin_hash TEXT
          );
          
          CREATE TABLE messages(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            subject TEXT,
            body TEXT,
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
            list_contact_id INTEGER REFERENCES list_contacts(id) -- for optin messages and other non-list things
          );
          
          CREATE TABLE deliveries(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            message_id INTEGER REFERENCES messages(id),
            list_contact_id INTEGER REFERENCES list_contacts(id),
            date_scheduled DATETIME NOT NULL,
            date_sent DATETIME,
            date_canceled DATETIME,
            unsub_hash TEXT
          );
          
          CREATE UNIQUE INDEX uq_list_contacts ON list_contacts(list_id, contact_id);
          CREATE UNIQUE INDEX uq_deliveries ON deliveries(message_id, list_contact_id);');
    }
}
