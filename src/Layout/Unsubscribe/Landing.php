<?php

namespace RoyallTheFourth\QuickList\Layout\Unsubscribe;

use RoyallTheFourth\QuickList\Layout\Base\LoggedOut;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Landing implements LayoutInterface
{
    private $csrf;
    private $hash;
    private $listName;
    private $prefix;

    public function __construct(string $listName, string $hash, string $csrf, string $prefix)
    {
        $this->listName = $listName;
        $this->hash = strip_tags($hash);
        $this->csrf = $csrf;
        $this->prefix = $prefix;
    }

    public function render(): string
    {
        $page = <<<page
<form method="POST" action="{$this->prefix}/unsubscribe">
If you wish to unsubscribe from {$this->listName}, click the button: 
<input type="hidden" name="hash" value="{$this->hash}" />
<input type="hidden" name="csrf" value="{$this->csrf}" />
<button>Unsubscribe</button>
</form>
page;

        return (new LoggedOut(
            "Unsubscribe from {$this->listName}",
            $page
        ))->render();
    }
}
