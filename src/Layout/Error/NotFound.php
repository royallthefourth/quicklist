<?php

namespace RoyallTheFourth\QuickList\Layout\Error;

use RoyallTheFourth\QuickList\Layout\Base\LoggedOut;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class NotFound implements LayoutInterface
{
    public function render(): string
    {
        return (new LoggedOut(
            'File Not Found',
            '<div id="error"><h1>404</h1><p>File Not Found</p></div>'
        ))->render();
    }
}
