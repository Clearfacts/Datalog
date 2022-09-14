<?php

namespace Datalog\Processor;

class DatadogTracingProcessor
{
    public function processRecord(array $record)
    {
        if (!extension_loaded('ddtrace')) {
            return $record;
        }

        $context = \DDTrace\current_context();
        $record['dd'] .= [
            'trace_id' => $context['trace_id'],
            'span_id' => $context['span_id'],
        ];

        return $record;
    }
}
