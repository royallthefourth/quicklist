<?php

namespace RoyallTheFourth\QuickList\Layout\Contact;

use function RoyallTheFourth\QuickList\Common\localDate;
use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Index implements LayoutInterface
{
    private $contacts;
    private $pagination;
    private $timezone;
    private $webPrefix;

    public function __construct(
        iterable $contacts,
        string $webPrefix,
        \DateTimeZone $timezone,
        string $pagination
    ) {
        $this->contacts = $contacts;
        $this->pagination = $pagination;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $rows = '';
        foreach ($this->contacts as ['id' => $id, 'email' => $email, 'date_added' => $dateAdded]) {
            $dateAdded = localDate($dateAdded, $this->timezone);
            $rows .= <<<row
<tr><td><a href="{$this->webPrefix}/contact/view/{$id}">{$email}</a></td><td>{$dateAdded}</td></tr>
row;
        }
        $table = <<<table
<h1>Contacts</h1>
<table>
<thead>
<tr>
<th>email</th>
<th>date added</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
</table>
{$this->pagination}
table;
        return (new LoggedIn('Contacts', $table, $this->webPrefix))->render();
    }
}
