<?php

namespace RoyallTheFourth\QuickList\Layout\MailingList;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class AddContactsForm implements LayoutInterface
{
    private $listName;
    private $webPrefix;

    public function __construct(
        string $webPrefix,
        string $listName = null
    ) {
        $this->listName = $listName;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $title = "Add contacts to {$this->listName}";

        $messageForm = <<<form
<h1>{$title}</h1>
<form class="main" method="POST">
<label>Send optin messages? <input type="checkbox" value="yes" name="optin" /></label>
<label for="emails">Add email addresses, one per line:</label>
<textarea required name="emails" id="emails"></textarea>
<button>Submit</button>
</form>
form;

        return (new LoggedIn($title, $messageForm, $this->webPrefix))->render();
    }
}
