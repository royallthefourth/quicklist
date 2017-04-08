#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

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
$app = new Application('quicklist', '1.0');

$app->add(new Contact\Add($db));
$app->add(new Contact\AddBulk($db));
$app->add(new Contact\Show($db));   // these use the identifier "show" internally because "list" is reserved

$app->add(new Delivery\Process($db, $config, $mailer));
$app->add(new Delivery\Schedule($db));
$app->add(new Delivery\Show($db, new \DateTimeZone($config['default_timezone'])));

$app->add(new MailingList\Add($db));
$app->add(new MailingList\OptInContact($db, $config['site_domain']));
$app->add(new MailingList\RemoveContact($db));
$app->add(new MailingList\Show($db));
$app->add(new MailingList\ShowContacts($db, new \DateTimeZone($config['default_timezone'])));

$app->add(new Message\Add($db));
$app->add(new Message\Show($db));

$app->add(new User\Add($db));
$app->add(new User\Reset($db));
$app->add(new User\Show($db));

$app->run();
