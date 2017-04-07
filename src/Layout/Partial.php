<?php

use RoyallTheFourth\HtmlDocument\Element\Anchor;
use RoyallTheFourth\HtmlDocument\Element\Division;
use RoyallTheFourth\HtmlDocument\Element\ListItem;
use RoyallTheFourth\HtmlDocument\Element\Text;
use RoyallTheFourth\HtmlDocument\Element\UnorderedList;
use RoyallTheFourth\HtmlDocument\Set\ElementSet;

function flash(string $message = ''): ElementSet
{
    return (new ElementSet())
        ->add((new Division())
            ->withChild(new Text($message))
            ->withId('flash'));
}

function nav(string $webPrefix): ElementSet
{
    return new ElementSet(
        (new UnorderedList())
            ->withChild((new ListItem())
                ->withChild((new Anchor())
                    ->withHref("{$webPrefix}/")
                    ->withChild(new Text('dashboard'))
                )
            )
            ->withChild((new ListItem())
                ->withChild((new Anchor())
                    ->withHref("{$webPrefix}/logout")
                    ->withChild(new Text('logout'))
                )
            )
    );
}
