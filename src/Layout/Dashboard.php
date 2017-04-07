<?php

namespace RoyallTheFourth\QuickList\Layout;

use RoyallTheFourth\HtmlDocument\Element\Text;
use RoyallTheFourth\HtmlDocument\Set\ElementSet;
use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;

class Dashboard implements LayoutInterface
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
            new ElementSet(new Text('TODO update dashboard')), // TODO update dashboard
            $this->webPrefix
        ))->render();
    }
}
