<?php

namespace RoyallTheFourth\QuickList\Layout\MailingList;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class View implements LayoutInterface
{
    private $activeContacts;
    private $deliveries;
    private $listId;
    private $messages;
    private $webPrefix;
    private $name;

    public function __construct(
        int $listId,
        string $name,
        int $messages,
        int $activeContacts,
        int $deliveries,
        string $webPrefix
    ) {
        $this->listId = $listId;
        $this->webPrefix = $webPrefix;
        $this->name = $name;
        $this->messages = $messages;
        $this->activeContacts = $activeContacts;
        $this->deliveries = $deliveries;
    }

    public function render(): string
    {
        $table = <<<table
<table>
<caption>{$this->name}</caption>
<thead>
<tr>
<th>active contacts</th>
<th>unique messages</th>
<th>deliveries sent</th>
</tr>
</thead>
<tbody>
<td><a href="{$this->webPrefix}/list/{$this->listId}/contacts/1">{$this->activeContacts}</a></td>
<td><a href="{$this->webPrefix}/list/{$this->listId}/messages/1">{$this->messages}</a></td>
<td><a href="{$this->webPrefix}/list/{$this->listId}/deliveries/1">{$this->deliveries}</a></td>
</tbody>
</table>
table;
        return (new LoggedIn('Lists', $table, $this->webPrefix))->render();
    }
}
