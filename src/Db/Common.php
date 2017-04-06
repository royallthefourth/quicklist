<?php

namespace RoyallTheFourth\QuickList\Db\Common;

use RoyallTheFourth\SmoothPdo\DataObject;

function connection(): DataObject
{
    return new DataObject('sqlite:' . __DIR__ . '../../../config/quicklist.db');
}
