<?php

namespace RoyallTheFourth\QuickList\Layout\Delivery;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Schedule implements LayoutInterface
{
    private $listId;
    private $listName;
    private $messageId;
    private $subject;
    private $timezone;
    private $webPrefix;

    public function __construct(
        int $listId,
        string $listName,
        int $messageId,
        string $subject,
        \DateTimeZone $timezone,
        string $webPrefix
    ) {
        $this->listId = $listId;
        $this->listName = $listName;
        $this->messageId = $messageId;
        $this->subject = $subject;
        $this->timezone = $timezone;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        $sendDate = (new \DateTimeImmutable('now', $this->timezone))->format('Y-m-d H:i:s');
        $title = "Send {$this->subject} to {$this->listName}";

        $messageForm = <<<form
<h1>{$title}</h1>
<form class="main" method="POST">
<input type="hidden" value="{$this->messageId}" name="messageId">
<input type="hidden" value="{$this->listId}" name="listId">
<label for="sendDate">Send date</label>
<input required type="text" id="sendDate" name="sendDate" value="{$sendDate}" />
<button>Schedule</button>
</form>
form;

        return (new LoggedIn($title, $messageForm, $this->webPrefix))->render();
    }
}
