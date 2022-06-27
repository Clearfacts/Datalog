<?php

declare(strict_types=1);

namespace Datalog\Formatter;

use Datalog\Tools\ArrayFlattener;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class KeyValueFormatter extends JsonFormatter
{
    public function format(LogRecord|array $record): string
    {
        if ($record instanceof LogRecord) {
            $record = $record->toArray();
        }

        return ArrayFlattener::getFlatKeyValueString(json_decode($this->toJson($record), true, 512, JSON_THROW_ON_ERROR));
    }
}
