<?php

namespace RoyallTheFourth\QuickList\Layout\MailingList;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;
use RoyallTheFourth\QuickList\Route\MailingList;

final class View implements LayoutInterface
{
    private $activeContacts;
    private $listId;
    private $messages;
    private $webPrefix;
    private $name;

    public function __construct(
        int $listId,
        string $name,
        int $messages,
        int $activeContacts,
        string $webPrefix
    ) {
        $this->listId = $listId;
        $this->webPrefix = $webPrefix;
        $this->name = $name;
        $this->messages = $messages;
        $this->activeContacts = $activeContacts;
    }

    public function render(): string
    {
        $addUrl = MailingList\addContacts($this->webPrefix, $this->listId);
        $contactsUrl = MailingList\contacts($this->webPrefix, $this->listId);
        $messagesUrl = MailingList\messages($this->webPrefix, $this->listId);

        $table = <<<table
<a href="{$addUrl}">Add contacts</a>
<table>
<caption>{$this->name}</caption>
<thead>
<tr>
<th>active contacts</th>
<th>unique messages</th>
</tr>
</thead>
<tbody>
<td><a href="{$contactsUrl}">{$this->activeContacts}</a></td>
<td><a href="{$messagesUrl}">{$this->messages}</a></td>
</tbody>
</table>
table;
        return (new LoggedIn($this->name, $table, $this->webPrefix))->render();
    }
}
