<?php

namespace RoyallTheFourth\QuickList\Tests\Db;

use Faker;
use RoyallTheFourth\QuickList\Db\Contact;
use RoyallTheFourth\SmoothPdo\DataObject;

class ContactTest extends Base
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
        $email = $faker->email;
        Contact\add($this->db, $email);
        static::assertCount(
            1,
            $this->db->query("SELECT * FROM contacts WHERE email = '{$email}'")->fetchAll(\PDO::FETCH_ASSOC)
        );

        static::assertGreaterThan(0, Contact\all($this->db));
    }

    public function testAddBulk()
    {
        $faker = Faker\Factory::create();
        $email1 = $faker->email;
        $email2 = $faker->email;
        Contact\addBulk($this->db, [$email1, $email2]);
        static::assertCount(
            2,
            $this->db
                ->query("SELECT * FROM contacts WHERE email IN('{$email1}', '{$email2}')")
                ->fetchAll(\PDO::FETCH_ASSOC)
        );
    }
}
