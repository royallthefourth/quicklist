<?php

namespace RoyallTheFourth\QuickList\Layout\Delivery;

use function RoyallTheFourth\QuickList\Common\localDate;
use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;
use RoyallTheFourth\QuickList\Route\Contact;
use RoyallTheFourth\QuickList\Route\Message;

final class Index implements LayoutInterface
{
    private $deliveries;
    private $pagination;
    private $timezone;
    private $webPrefix;

    public function __construct(
        iterable $deliveries,
        string $webPrefix,
        \DateTimeZone $timezone,
        string $pagination
    ) {
        $this->deliveries = $deliveries;
        $this->pagination = $pagination;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $rows = '';
        foreach ($this->deliveries as ['message_id' => $messageId,
                 'subject' => $subject,
                 'contact_id' => $contactId,
                 'email' => $email,
                 'date_scheduled' => $dateScheduled,
                 'date_sent' => $dateSent]
        ) {
            $messageUrl = Message\view($this->webPrefix, $messageId);
            $contactUrl = Contact\view($this->webPrefix, $contactId);
            $dateScheduled = localDate($dateScheduled, $this->timezone);
            $dateSent = localDate($dateSent, $this->timezone);
            $rows .= <<<row
<tr>
<td><a href="{$messageUrl}">{$subject}</a></td>
<td><a href="{$contactUrl}">{$email}</a></td>
<td>{$dateScheduled}</td>
<td>{$dateSent}</td>
</tr>
row;
        }
        $table = <<<table
<table>
<caption>Deliveries</caption>
<thead>
<tr>
<th>subject</th>
<th>recipient</th>
<th>date scheduled</th>
<th>date sent</th>
</tr>
</thead>
<tbody>
{$rows}
</tbody>
</table>
{$this->pagination}
table;
        return (new LoggedIn('Deliveries', $table, $this->webPrefix))->render();
    }
}
