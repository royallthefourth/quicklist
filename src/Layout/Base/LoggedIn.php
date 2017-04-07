<?php

namespace RoyallTheFourth\QuickList\Layout\Base;

use RoyallTheFourth\HtmlDocument\Set\ElementSet;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class LoggedIn implements LayoutInterface
{
    private $body;
    private $flash;
    private $nav;
    private $title;

    public function __construct(string $title, ElementSet $body, string $webPrefix)
    {
        $this->title = htmlspecialchars($title);
        $this->body = $body;
        $this->nav = nav($webPrefix);
        $this->flash = $_SESSION['flash'] ?? '';
    }

    public function render(): string
    {
        unset($_SESSION['flash']);
        return (new LoggedOut($this->title, $this->body, $this->flash, $this->nav))->render();
    }
}
