<?php

namespace RoyallTheFourth\QuickList\Layout\Error;

use RoyallTheFourth\HtmlDocument\Element\Division;
use RoyallTheFourth\HtmlDocument\Element\Heading;
use RoyallTheFourth\HtmlDocument\Element\Paragraph;
use RoyallTheFourth\HtmlDocument\Element\Text;
use RoyallTheFourth\HtmlDocument\Set\ElementSet;
use RoyallTheFourth\QuickList\Layout\Base\LoggedOut;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class NotFound implements LayoutInterface
{
    public function render(): string
    {
        return (new LoggedOut(
            'File Not Found',
            (new ElementSet())
                ->add((new Division())
                    ->withChild((new Heading(1))
                        ->withChild(new Text('404'))
                    )
                    ->withChild((new Paragraph())
                        ->withChild(new Text('File Not Found'))
                    )->withId('error')
                )
        ))->render();
    }
}
