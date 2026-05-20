<?php

declare(strict_types=1);

namespace Tests\Datalog\Formatter;

use Datalog\Formatter\KeyValueFormatter;
use Datalog\Processor\DatadogTracingProcessor;
use Datalog\Processor\SessionRequestProcessor;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\Datalog\TestCase;

class KeyValueFormatterTest extends TestCase
{
    private KeyValueFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new KeyValueFormatter(appendNewline: false);
    }

    public function testRootLevelKeysAreHoistedFromExtra(): void
    {
        $record = new LogRecord(
            datetime: new \DateTimeImmutable('2024-01-01 00:00:00'),
            channel: 'test',
            level: Level::Info,
            message: 'test message',
            extra: [
                DatadogTracingProcessor::KEY_DD => [
                    'trace_id' => '123',
                    'span_id' => '456',
                ],
                SessionRequestProcessor::KEY_REQUEST_ID => 'req-1',
                SessionRequestProcessor::KEY_SESSION_ID => 'sess-1',
                SessionRequestProcessor::KEY_HTTP_URL => 'https://example.com',
                SessionRequestProcessor::KEY_HTTP_METHOD => 'GET',
                SessionRequestProcessor::KEY_HTTP_USERAGENT => 'Mozilla',
                SessionRequestProcessor::KEY_HTTP_REFERER => 'https://ref.com',
                SessionRequestProcessor::KEY_HTTP_X_FORWARDED_FOR => '1.2.3.4',
            ],
        );

        $output = $this->formatter->format($record);

        // Root level keys should NOT be prefixed with "extra."
        $this->assertStringContainsString('dd.trace_id="123"', $output);
        $this->assertStringContainsString('dd.span_id="456"', $output);
        $this->assertStringContainsString('request_id="req-1"', $output);
        $this->assertStringContainsString('session_id="sess-1"', $output);
        $this->assertStringContainsString('http.url="https://example.com"', $output);
        $this->assertStringContainsString('http.method="GET"', $output);

        // Should not appear under extra
        $this->assertStringNotContainsString('extra.dd', $output);
        $this->assertStringNotContainsString('extra.request_id', $output);
        $this->assertStringNotContainsString('extra.session_id', $output);
        $this->assertStringNotContainsString('extra.http.', $output);
    }

    public function testNonRootLevelKeysStayInExtra(): void
    {
        $record = new LogRecord(
            datetime: new \DateTimeImmutable('2024-01-01 00:00:00'),
            channel: 'test',
            level: Level::Info,
            message: 'test message',
            extra: [
                'correlation_id' => 'corr-1',
                'user-id' => '42',
            ],
        );

        $output = $this->formatter->format($record);

        $this->assertStringContainsString('extra.correlation_id="corr-1"', $output);
        $this->assertStringContainsString('extra.user-id="42"', $output);
    }

    public function testMixedRootAndExtraKeys(): void
    {
        $record = new LogRecord(
            datetime: new \DateTimeImmutable('2024-01-01 00:00:00'),
            channel: 'test',
            level: Level::Info,
            message: 'test message',
            extra: [
                DatadogTracingProcessor::KEY_DD => ['trace_id' => '123', 'span_id' => '456'],
                'correlation_id' => 'corr-1',
                SessionRequestProcessor::KEY_REQUEST_ID => 'req-1',
            ],
        );

        $output = $this->formatter->format($record);

        // Hoisted to root
        $this->assertStringContainsString('dd.trace_id="123"', $output);
        $this->assertStringContainsString('request_id="req-1"', $output);

        // Stays in extra
        $this->assertStringContainsString('extra.correlation_id="corr-1"', $output);

        // Not duplicated
        $this->assertStringNotContainsString('extra.dd', $output);
        $this->assertStringNotContainsString('extra.request_id', $output);
    }

    public function testExtraIsRemovedWhenEmpty(): void
    {
        $record = new LogRecord(
            datetime: new \DateTimeImmutable('2024-01-01 00:00:00'),
            channel: 'test',
            level: Level::Info,
            message: 'test message',
            extra: [
                SessionRequestProcessor::KEY_REQUEST_ID => 'req-1',
            ],
        );

        $output = $this->formatter->format($record);

        $this->assertStringContainsString('request_id="req-1"', $output);
        $this->assertStringNotContainsString('extra', $output);
    }
}
