<?php

namespace RoyallTheFourth\QuickList\Layout\MailingList;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Index implements LayoutInterface
{
    private $lists;
    private $pagination;
    private $webPrefix;

    public function __construct(
        iterable $lists,
        string $webPrefix,
        string $pagination
    ) {
        $this->lists = $lists;
        $this->pagination = $pagination;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $rows = '';
        foreach ($this->lists as ['id' => $id,
                 'name' => $name,
                 'contacts' => $contacts,
                 'messages' => $messages,
                 'deliveries' => $deliveries]) {
            $rows .= <<<row
<tr>
<td>{$name}</td>
<td><a href="{$this->webPrefix}/list/{$id}/contacts">{$contacts}</a></td>
<td><a href="{$this->webPrefix}/list/{$id}/messages">{$messages}</a></td>
<td><a href="{$this->webPrefix}/list/{$id}/deliveries">{$deliveries}</a></td>
</tr>
row;
        }
        $table = <<<table
<h1>Lists</h1>
<table>
<thead>
<tr>
<th>name</th>
<th>contacts</th>
<th>messages</th>
<th>deliveries</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
</table>
{$this->pagination}
table;
        return (new LoggedIn('Lists', $table, $this->webPrefix))->render();
    }
}
