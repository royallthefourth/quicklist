<?php

namespace RoyallTheFourth\QuickList\Layout\Message;

use function RoyallTheFourth\QuickList\Common\localDate;
use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Index implements LayoutInterface
{
    private $messages;
    private $pagination;
    private $timezone;
    private $webPrefix;

    public function __construct(
        iterable $messages,
        string $webPrefix,
        int $page,
        \DateTimeZone $timezone,
        string $pagination
    ) {
        $this->messages = $messages;
        $this->pagination = $pagination;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $rows = '';
        foreach ($this->messages as ['id' => $id, 'subject' => $subject, 'date_added' => $dateAdded]) {
            $dateAdded = localDate($dateAdded, $this->timezone);
            $rows .= <<<row
<tr><td><a href="{$this->webPrefix}/message/view/{$id}">{$subject}</a></td><td>{$dateAdded}</td></tr>
row;
        }
        $table = <<<table
<a href="{$this->webPrefix}/message/add">Add a new message</a>
<table>
<caption></caption>
<thead>
<tr>
<th>subject</th>
<th>date added</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
</table>
{$this->pagination}
table;
        return (new LoggedIn('Messages', $table, $this->webPrefix))->render();
    }
}
