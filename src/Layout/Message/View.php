<?php

namespace RoyallTheFourth\QuickList\Layout\Message;

use function RoyallTheFourth\QuickList\Common\localDate;
use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;
use RoyallTheFourth\QuickList\Route;

final class View implements LayoutInterface
{
    private $message;
    private $deliveries;
    private $lists;
    private $timezone;
    private $webPrefix;

    public function __construct(
        array $message,
        iterable $deliveries,
        iterable $lists,
        string $webPrefix,
        \DateTimeZone $timezone
    ) {
        $this->message = $message;
        $this->deliveries = $deliveries;
        $this->lists = $lists;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $dateAdded = localDate($this->message['date_added'], $this->timezone);
        $deliveries = $this->deliveryTable($this->deliveries, $this->webPrefix);
        $lists = $this->listDropdown($this->lists);
        $scheduleUrl = Route\Delivery\schedule($this->webPrefix);
        $editUrl = Route\Message\edit($this->webPrefix, $this->message['id']);

        $contactInfo = <<<contactInfo
<h1>{$this->message['subject']} 
<span>added {$dateAdded} <a href="{$editUrl}">edit</a></span></h1>
<section>
<pre>{$this->message['body']}</pre>
<form action="{$scheduleUrl}" method="GET">
<input type="hidden" name="messageId" value="{$this->message['id']}" />
Send this message to a list: <select name="listId">{$lists}</select>
<button>Schedule</button>
</form>
</section>
{$deliveries}
contactInfo;
        return (new LoggedIn($this->message['subject'], $contactInfo, $this->webPrefix))->render();
    }

    private function deliveryTable(iterable $deliveries, string $webPrefix): string
    {
        $rows = '';

        foreach ($deliveries as ['contact_id' => $contactId,
                 'email' => $email,
                 'list_id' => $listId,
                 'list_name' => $listName,
                 'date_scheduled' => $dateScheduled,
                 'date_sent' => $dateSent]) {
            $dateScheduled = localDate($dateScheduled, $this->timezone);
            $dateSent = localDate($dateSent, $this->timezone);
            $contactUrl = Route\Contact\view($this->webPrefix, $contactId);
            $listUrl = Route\MailingList\view($this->webPrefix, $listId);
            $rows .= <<<row
<tr>
<td><a href="{$contactUrl}">{$email}</a></td>
<td><a href="{$listUrl}">$listName</a></td>
<td>{$dateScheduled}</td>
<td>{$dateSent}</td>
</tr>
row;
        }

        return <<<table
<table>
<caption>Deliveries</caption>
<thead>
<tr>
<th>recipient</th>
<th>list</th>
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

    private function listDropdown(iterable $lists): string
    {
        $out = '';
        foreach ($lists as ['id' => $id, 'name' => $name]) {
            $out .= "<option value=\"{$id}\">{$name}</option>";
        }

        return $out;
    }
}
