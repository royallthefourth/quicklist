<?php

namespace RoyallTheFourth\QuickList\Layout\Optin;

use RoyallTheFourth\HtmlDocument\Element\Text;
use RoyallTheFourth\HtmlDocument\Set\ElementSet;
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
            (new ElementSet())
                ->add(new Text("You are now subscribed to {$this->listName}."))
        )
        )->render();
    }
}
