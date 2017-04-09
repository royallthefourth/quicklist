<?php

namespace RoyallTheFourth\QuickList\Layout\Login;

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
        $form = <<<form
<form method="POST" action="{$this->prefix}/login" id="login">
<input type="hidden" name="csrf" value="{$this->csrf}" />
<label for="username">Username:</label>
<input required type="text" name="username" id="username" />
<label for="password">Password:</label>
<input required type="password" name="password" id="password" />
<button>Login</button>
</form>
form;

        return (new LoggedOut(
            'Login',
            $form,
            $this->flash
        ))->render();
    }
}
