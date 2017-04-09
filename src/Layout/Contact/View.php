<?php

namespace RoyallTheFourth\QuickList\Layout\Contact;

use function RoyallTheFourth\QuickList\Common\localDate;
use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class View implements LayoutInterface
{
    private $contact;
    private $deliveries;
    private $lists;
    private $timezone;
    private $webPrefix;

    public function __construct(
        array $contact,
        iterable $lists,
        iterable $deliveries,
        string $webPrefix,
        \DateTimeZone $timezone
    ) {
        $this->contact = $contact;
        $this->lists = $lists;
        $this->deliveries = $deliveries;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $dateAdded = localDate($this->contact['date_added'], $this->timezone);
        $lists = $this->listTable($this->lists, $this->webPrefix);
        $deliveries = $this->deliveryTable($this->deliveries, $this->webPrefix);

        $contactInfo = <<<contactInfo
<h1>{$this->contact['email']} <span>added {$dateAdded}</span></h1>
{$lists}
{$deliveries}
contactInfo;
        return (new LoggedIn($this->contact['email'], $contactInfo, $this->webPrefix))->render();
    }

    private function listTable(iterable $lists, string $webPrefix): string
    {
        $rows = '';

        foreach ($lists as ['id' => $listId,
                 'name' => $name,
                 'date_added' => $dateAdded,
                 'date_unsubscribed' => $dateUnsubscribed]) {
            $dateAdded = localDate($dateAdded, $this->timezone);
            $dateUnsubscribed = localDate($dateUnsubscribed, $this->timezone);
            $rows .= "<tr>
<td><a href=\"{$webPrefix}/list/view/{$listId}\">{$name}</a></td>
<td>{$dateAdded}</td>
<td>{$dateUnsubscribed}</td>
</tr>";
        }

        return <<<table
<table>
<caption>Lists</caption>
<thead>
<tr>
<th>name</th>
<th>date added</th>
<th>date unsubscribed</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
</table>
table;
    }

    private function deliveryTable(iterable $deliveries, string $webPrefix): string
    {
        $rows = '';

        foreach ($deliveries as ['message_id' => $messageId,
                 'subject' => $subject,
                 'date_scheduled' => $dateScheduled,
                 'date_sent' => $dateSent]) {
            $dateScheduled = localDate($dateScheduled, $this->timezone);
            $dateSent = localDate($dateSent, $this->timezone);
            $rows .= "<tr>
<td><a href=\"{$webPrefix}/message/view/{$messageId}\">{$subject}</a></td>
<td>{$dateScheduled}</td>
<td>{$dateSent}</td>
</tr>";
        }

        return <<<table
<table>
<caption>Deliveries</caption>
<thead>
<tr>
<th>subject</th>
<th>date scheduled</th>
<th>date sent</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
</table>
table;
    }
}
