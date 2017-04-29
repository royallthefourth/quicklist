<?php

namespace RoyallTheFourth\QuickList\Tests;

use PHPUnit\Framework\TestCase;
use RoyallTheFourth\QuickList\Delivery;

class DeliveryTest extends TestCase
{
    public function testUnsubLink()
    {
        $message = Delivery\appendUnsubLink('hello', 'asdf', 'example.com');
        static::assertContains('hello', $message);
        static::assertContains('asdf', $message);
        static::assertContains('example.com', $message);
    }
}
