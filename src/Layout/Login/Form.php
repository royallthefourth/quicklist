<?php

namespace RoyallTheFourth\QuickList\Layout\Login;

use RoyallTheFourth\HtmlDocument\Element\Button;
use RoyallTheFourth\HtmlDocument\Element;
use RoyallTheFourth\HtmlDocument\Element\Input;
use RoyallTheFourth\HtmlDocument\Element\Text;
use RoyallTheFourth\HtmlDocument\Set\ElementSet;
use RoyallTheFourth\QuickList\Layout\Base\LoggedOut;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Form implements LayoutInterface
{
    private $csrf;
    private $flash;
    private $prefix;

    public function __construct(string $csrf, string $prefix)
    {
        $this->csrf = $csrf;
        $this->prefix = $prefix;
        $this->flash = $_SESSION['flash'] ?? '';
        unset($_SESSION['flash']);
    }

    public function render(): string
    {
        return (new LoggedOut(
            'Login',
            (new ElementSet())
                ->add(
                    (new Element\Form())
                        ->withMethod('POST')
                        ->withAction("{$this->prefix}/login")
                        ->withChild(
                            (new Input())
                                ->withType('hidden')
                                ->withName('csrf')
                                ->withValue($this->csrf)
                        )
                        ->withChild(
                            (new Element\Label())
                                ->withFor('username')
                                ->withChild(new Text('Username:'))
                        )
                        ->withChild(
                            (new Input())
                                ->withType('text')
                                ->withName('username')
                                ->withRequired()
                                ->withId('username')
                        )
                        ->withChild(
                            (new Element\Label())
                                ->withFor('password')
                                ->withChild(new Text('Password:'))
                        )
                        ->withChild(
                            (new Input())
                                ->withType('password')
                                ->withName('password')
                                ->withRequired()
                                ->withId('password')
                        )
                        ->withChild(
                            (new Button())
                                ->withChild(new Text('Login'))
                        )
                        ->withId('login')
                ),
            $this->flash
        )
        )->render();
    }
}
