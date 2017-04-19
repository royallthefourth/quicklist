<?php

namespace RoyallTheFourth\QuickList\Tests;

use PHPUnit\Framework\TestCase;
use function RoyallTheFourth\QuickList\Common\iterableToArray;
use function RoyallTheFourth\QuickList\Common\onlyValidEmails;

class CommonTest extends TestCase
{
    public function testValidEmails()
    {
        $emails = iterableToArray(onlyValidEmails([
            'test@example.com',
            'arglebargle'
        ]));

        static::assertContains('test@example.com', $emails);
        static::assertNotContains('arglebargle', $emails);
    }
}
