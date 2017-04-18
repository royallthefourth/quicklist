#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use function RoyallTheFourth\QuickList\Common\currentVersion;
use RoyallTheFourth\QuickList\Console\Contact;
use RoyallTheFourth\QuickList\Console\Delivery;
use RoyallTheFourth\QuickList\Console\MailingList;
use RoyallTheFourth\QuickList\Console\Message;
use RoyallTheFourth\QuickList\Console\User;
use RoyallTheFourth\QuickList\Db;
use Symfony\Component\Console\Application;

$config = RoyallTheFourth\QuickList\Common\config();

$db = Db\Common\connection();
$mailer = RoyallTheFourth\QuickList\Common\mailer($config);
$timezone = new \DateTimeZone($config['default_timezone']);
$app = new Application('Quicklist', currentVersion());

$app->add(new Contact\Add($db));
$app->add(new Contact\AddBulk($db));
// these use the identifier "show" internally because "list" is reserved
$app->add(new Contact\Show($db, $timezone));

$app->add(new Delivery\Process($db, $config, $mailer));
$app->add(new Delivery\Schedule($db, $timezone));
$app->add(new Delivery\Show($db, $timezone));

$app->add(new MailingList\Add($db));
$app->add(new MailingList\AddContactBulk($db, $config['site_domain']));
$app->add(new MailingList\OptInContact($db, $config['site_domain']));
$app->add(new MailingList\RemoveContact($db));
$app->add(new MailingList\Show($db));
$app->add(new MailingList\ShowContacts($db, $timezone));

$app->add(new Message\Add($db));
$app->add(new Message\Show($db));

$app->add(new User\Add($db));
$app->add(new User\Reset($db));
$app->add(new User\Show($db));

$app->run();
