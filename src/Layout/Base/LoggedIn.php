<?php

namespace RoyallTheFourth\QuickList\Layout\Base;

use function RoyallTheFourth\QuickList\Layout\Partial\nav;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class LoggedIn implements LayoutInterface
{
    private $body;
    private $flash;
    private $nav;
    private $title;

    public function __construct(string $title, string $body, string $webPrefix)
    {
        $this->title = htmlspecialchars($title);
        $this->body = $body;
        $this->nav = nav($webPrefix);
        $this->flash = $_SESSION['flash'] ?? '';
    }

    public function render(): string
    {
        return (new LoggedOut($this->title, $this->body, $this->flash, $this->nav))->render();
    }
}
