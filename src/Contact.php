<?php

namespace RoyallTheFourth\QuickList\Contact;

use Respect\Validation\Validator;

function onlyValidEmails(array $emails): array
{
    return array_filter($emails, function (string $email) {
        return Validator::email()->validate($email);
    });
}
