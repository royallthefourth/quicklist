<?php

namespace RoyallTheFourth\QuickList\Tests\Db;

use Faker;
use RoyallTheFourth\QuickList\Db\Message;
use RoyallTheFourth\SmoothPdo\DataObject;

class MessageTest extends Base
{
    /** @var  $db DataObject */
    private $db;

    protected function setUp()
    {
        $this->db = $this->dbConnection();
    }

    public function testAdd()
    {
        $faker = Faker\Factory::create();
        $subject = $faker->sentence(3, false);
        $body = $faker->sentence();
        static::assertGreaterThanOrEqual(0, Message\add($this->db, $subject, $body));
        static::assertEquals($subject, Message\all($this->db)[0]['subject']);
    }
}
