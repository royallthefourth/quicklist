<?php

namespace RoyallTheFourth\QuickList\Layout\MailingList;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;
use RoyallTheFourth\QuickList\Route\MailingList;

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
        $addList = MailingList\add($this->webPrefix);
        $rows = '';
        foreach ($this->lists as ['id' => $id, 'name' => $name]) {
            $url = MailingList\view($this->webPrefix, $id);

            $rows .= <<<row
<tr>
<td><a href="{$url}">{$name}</a></td>
</tr>
row;
        }
        $table = <<<table
<a href="{$addList}">Add a new list</a>
<table>
<caption>Lists</caption>
<thead>
<tr>
<th>name</th>
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
