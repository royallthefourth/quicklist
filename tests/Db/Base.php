<?php

namespace RoyallTheFourth\QuickList\Tests\Db;

use Phinx\Console;
use Phinx\Wrapper;
use PHPUnit\Framework\TestCase;
use RoyallTheFourth\SmoothPdo\DataObject;

class Base extends TestCase
{
    protected static $dbPath = __DIR__ . '/../../config/quicklist.test.db';

    protected function dbConnection(): DataObject
    {
        return new DataObject('sqlite:'. static::$dbPath);
    }

    public static function setUpBeforeClass()
    {
        $wrap = new Wrapper\TextWrapper(
            new Console\PhinxApplication(),
            [
                'configuration' => __DIR__ . '/../../phinx.yml',
                'environment' => 'test',
                'parser' => 'yaml'
            ]
        );
        $wrap->getMigrate('test');
    }

    public static function tearDownAfterClass()
    {
        unlink(static::$dbPath);
    }
}
