<?php

namespace RoyallTheFourth\QuickList\Layout\Unsubscribe;

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
            "Unsubscribe from {$this->listName}",
            "You have unsubscribed from {$this->listName}."
        ))->render();
    }
}
