<?php

namespace RoyallTheFourth\QuickList\Layout;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;

final class Dashboard implements LayoutInterface
{
    private $webPrefix;

    public function __construct(string $webPrefix)
    {
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        return (new LoggedIn(
            'Dashboard',
            'Welcome to Quicklist!',
            $this->webPrefix
        ))->render();
    }
}
