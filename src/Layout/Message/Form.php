<?php

namespace RoyallTheFourth\QuickList\Layout\Message;

use RoyallTheFourth\QuickList\Layout\Base\LoggedIn;
use RoyallTheFourth\QuickList\Layout\LayoutInterface;

final class Form implements LayoutInterface
{
    private $message;
    private $webPrefix;

    public function __construct(
        string $webPrefix,
        array $message = null
    ) {
        $this->message = $message;
        $this->webPrefix = $webPrefix;
    }

    public function render(): string
    {
        [$title, $messageIdField, $subject, $body] = $this->defaults($this->message);

        $messageForm = <<<form
<h1>{$title}</h1>
<form class="main" method="POST">
{$messageIdField}
<label for="subject">Subject</label>
<input required type="text" id="subject" name="subject" value="{$subject}" />
<label for="body">Body</label>
<textarea required rows="12" id="body" name="body">{$body}</textarea>
<button>Submit</button>
</form>
form;

        return (new LoggedIn($title, $messageForm, $this->webPrefix))->render();
    }

    private function defaults(?array $message): array
    {
        if ($message === null) {
            $title = 'Add Message';
            $messageIdField = '';
            $subject = '';
            $body = '';
        } else {
            $title = "Edit {$message['subject']}";
            $messageIdField = "<input type=\"hidden\" value=\"{$message['id']}\" name=\"messageId\" />";
            $subject = $message['subject'];
            $body = $message['body'];
        }

        return [$title, $messageIdField, $subject, $body];
    }
}
