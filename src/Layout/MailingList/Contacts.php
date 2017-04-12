<?php

namespace RoyallTheFourth\QuickList\Layout\MailingList;

use function RoyallTheFourth\QuickList\Common\localDate;
use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Contacts implements LayoutInterface
{
    private $contacts;
    private $listName;
    private $pagination;
    private $timezone;
    private $webPrefix;

    public function __construct(
        iterable $contacts,
        string $listName,
        string $webPrefix,
        string $pagination,
        \DateTimeZone $timezone
    ) {
        $this->contacts = $contacts;
        $this->listName = $listName;
        $this->pagination = $pagination;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $rows = '';
        foreach ($this->contacts as ['id' => $id,
                 'email' => $email,
                 'date_added' => $date_added,
                 'date_optin' => $date_optin,
                 'date_unsubscribed' => $date_unsubscribed]) {

            $date_added = localDate($date_added, $this->timezone);
            $date_optin = localDate($date_optin, $this->timezone);
            $date_unsubscribed = localDate($date_unsubscribed, $this->timezone);

            $rows .= <<<row
<tr>
<td><a href="{$this->webPrefix}/contact/view/{$id}">{$email}</td>
<td>{$date_added}</td>
<td>{$date_optin}</td>
<td>{$date_unsubscribed}</td>
</tr>
row;
        }
        $table = <<<table
<table>
<caption>{$this->listName} Contacts</caption>
<thead>
<tr>
<th>email</th>
<th>date added</th>
<th>optin date</th>
<th>unsubscribe date</th>
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
