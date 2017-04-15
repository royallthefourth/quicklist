<?php

namespace RoyallTheFourth\QuickList\Layout\MailingList;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Form implements LayoutInterface
{
    private $listId;
    private $listName;
    private $webPrefix;

    public function __construct(
        string $webPrefix,
        int $listId = null,
        string $listName = null
    ) {
        $this->listId = $listId;
        $this->listName = $listName;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        [$title, $listIdField, $name] = $this->defaults($this->listId, $this->listName);

        $messageForm = <<<form
<h1>{$title}</h1>
<form class="main" method="POST">
{$listIdField}
<label for="name">Name</label>
<input type="text" id="name" name="name" value="{$name}" />
<button>Submit</button>
</form>
form;

        return (new LoggedIn($title, $messageForm, $this->webPrefix))->render();
    }

    private function defaults($listId = null, $listName = null): array
    {
        if ($listId === null) {
            $title = 'Add List';
            $listIdField = '';
            $name = '';
        } else {
            $title = "Edit {$listName}";
            $listIdField = "<input type=\"hidden\" value=\"{$listId}\" name=\"listId\" />";
            $name = $listName;
        }

        return [$title, $listIdField, $name];
    }
}
