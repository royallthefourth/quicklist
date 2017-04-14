<?php

namespace RoyallTheFourth\QuickList\Layout\MailingList;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;
use RoyallTheFourth\QuickList\Route\Message;

final class Messages implements LayoutInterface
{
    private $messages;
    private $listName;
    private $pagination;
    private $webPrefix;

    public function __construct(
        iterable $messages,
        string $listName,
        string $webPrefix,
        string $pagination
    ) {
        $this->messages = $messages;
        $this->listName = $listName;
        $this->pagination = $pagination;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $rows = '';
        foreach ($this->messages as ['id' => $id, 'subject' => $subject]) {
            $url = Message\view($this->webPrefix, $id);
            $rows .= <<<row
<tr>
<td><a href="{$url}">{$subject}</td>
</tr>
row;
        }

        $table = <<<table
<table>
<caption>{$this->listName} Messages</caption>
<thead>
<tr>
<th>subject</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
</table>
{$this->pagination}
table;
        return (new LoggedIn("{$this->listName} Messages", $table, $this->webPrefix))->render();
    }
}
