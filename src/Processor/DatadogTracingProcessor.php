<?php

namespace Datalog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class DatadogTracingProcessor implements ProcessorInterface
{
    public const KEY_DD = 'dd';

    public function __invoke(LogRecord $record): LogRecord
    {
        if (!extension_loaded('ddtrace')) {
            return $record;
        }

        $context = \DDTrace\current_context();
        $record->extra = array_merge($record->extra, [
            self::KEY_DD => [
                'trace_id' => $context['trace_id'],
                'span_id' => $context['span_id'],
            ],
        ]);

        return $record;
    }
}
