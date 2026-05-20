<?php

declare(strict_types=1);

namespace Datalog\Formatter;

use Datalog\Processor\DatadogTracingProcessor;
use Datalog\Processor\SessionRequestProcessor;
use Datalog\Tools\ArrayFlattener;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class KeyValueFormatter extends JsonFormatter
{
    /**
     * Keys that processors historically placed at the root level of the log record
     * rather than inside 'extra'. In Monolog 3 processors can only write to 'extra'
     * or 'context', so we hoist these specific keys back to the root during formatting
     * to preserve the original log structure.
     */
    private const ROOT_LEVEL_KEYS = [
        DatadogTracingProcessor::KEY_DD,
        SessionRequestProcessor::KEY_REQUEST_ID,
        SessionRequestProcessor::KEY_SESSION_ID,
        SessionRequestProcessor::KEY_HTTP_URL,
        SessionRequestProcessor::KEY_HTTP_METHOD,
        SessionRequestProcessor::KEY_HTTP_USERAGENT,
        SessionRequestProcessor::KEY_HTTP_REFERER,
        SessionRequestProcessor::KEY_HTTP_X_FORWARDED_FOR,
    ];

    public function format(LogRecord $record): string
    {
        // Roundtrip through JSON to normalize types that ArrayFlattener cannot
        // handle (e.g. DateTimeImmutable) into plain scalars.
        $data = json_decode($this->toJson($record->toArray()), true);

        foreach (self::ROOT_LEVEL_KEYS as $key) {
            if (array_key_exists($key, $data['extra'])) {
                $data[$key] = $data['extra'][$key];
                unset($data['extra'][$key]);
            }
        }

        if (empty($data['extra'])) {
            unset($data['extra']);
        }

        return ArrayFlattener::getFlatKeyValueString($data)
            . ($this->appendNewline ? "\n" : '');
    }
}
