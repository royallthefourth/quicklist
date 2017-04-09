<?php

namespace RoyallTheFourth\QuickList\Layout\Optin;

use RoyallTheFourth\QuickList\Layout\Base\LoggedOut;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Confirmation implements LayoutInterface
{
    private $listName;

    public function __construct(string $listName)
    {
        $this->listName = $listName;
    }

    public function render(): string
    {
        return (new LoggedOut(
            "Subscribe to {$this->listName}",
            "You are now subscribed to {$this->listName}"
        ))->render();
    }
}
