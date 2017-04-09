<?php

namespace RoyallTheFourth\QuickList\Layout\Base;

use function RoyallTheFourth\QuickList\Layout\Partial\flash;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class LoggedOut implements LayoutInterface
{
    private $body;
    private $flash;
    private $nav;
    private $title;

    public function __construct(string $title, string $body, string $flash = '', string $nav = '')
    {
        $this->title = htmlspecialchars($title);
        $this->body = $body;
        $this->nav = $nav;
        $this->flash = flash($flash);
    }

    public function render(): string
    {
        unset($_SESSION['flash']);
        return <<<html
            <!doctype html>
            <html lang="en">
            <head>
            <title>{$this->title} | Quicklist</title>
            <link rel="stylesheet" href="/style.css" />
            <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <body>
            <header>
            quicklist
            <nav>{$this->nav}</nav>
            </header>
            <main>
            {$this->flash}
            {$this->body}
</main>
<footer></footer>
</header>
</body>
</head>
</html>
html;
    }
}
