<?php

namespace RoyallTheFourth\QuickList\Db;

use RoyallTheFourth\SmoothPdo\DataObject;

final class Common {
    public static function connection(): DataObject
    {
        return new DataObject('sqlite:' . __DIR__ . '../../../config/quicklist.db');
    }
}
