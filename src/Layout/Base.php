<?php

namespace RoyallTheFourth\QuickList\Layout;

use RoyallTheFourth\HtmlDocument\Document;
use RoyallTheFourth\HtmlDocument\Element\Body;
use RoyallTheFourth\HtmlDocument\Element\Footer;
use RoyallTheFourth\HtmlDocument\Element\Head;
use RoyallTheFourth\HtmlDocument\Element\Header;
use RoyallTheFourth\HtmlDocument\Element\Html;
use RoyallTheFourth\HtmlDocument\Element\Link;
use RoyallTheFourth\HtmlDocument\Element\Main;
use RoyallTheFourth\HtmlDocument\Element\Meta;
use RoyallTheFourth\HtmlDocument\Element\Nav;
use RoyallTheFourth\HtmlDocument\Element\Text;
use RoyallTheFourth\HtmlDocument\Element\Title;
use RoyallTheFourth\HtmlDocument\Set\ElementSet;

final class Base implements LayoutInterface
{
    private $document;
    private $title;
    private $body;

    public function __construct(string $title, ElementSet $body)
    {
        $this->document = new Document();
        $this->title = htmlspecialchars($title);
        $this->body = $body;
    }

    public function render(): string
    {
        return $this->document->add(
            (new Html())
                ->withAttribute('lang', 'en')
                ->withChild(
                    (new Head())
                        ->withChild(
                            (new Title())
                                ->withChild(new Text("{$this->title} | Quicklist"))
                        )
                        ->withChild(
                            (new Link())
                                ->withAttribute('rel', 'stylesheet')
                                ->withAttribute('href', '/style.css')
                        )
                        ->withChild(
                            (new Meta())
                                ->withAttribute('charset', 'utf-8')
                        )
                        ->withChild(
                            (new Meta())
                                ->withAttribute('viewport', 'width=device-width, initial-scale=1')
                        )
                )
                ->withChild(
                    (new Body())
                        ->withChild(
                            (new Header())
                                ->withChild(new Text('quicklist'))
                                ->withChild(new Nav())
                        )
                        ->withChild(new Main(null, $this->body))
                        ->withChild(new Footer())
                )
        )->render();
    }
}
