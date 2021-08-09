<?php

namespace Datalog\Formatter;

use Datalog\Tools\ArrayFlattener;
use Monolog\Formatter\JsonFormatter;

class KeyValueFormatter extends JsonFormatter
{
    public function format(array $record): string
    {
        return ArrayFlattener::getFlatKeyValueString(json_decode(parent::toJson($record), true));
    }
}
