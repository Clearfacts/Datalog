<?php
declare(strict_types=1);

namespace Tests\Datalog\Correlation\Http;

use Datalog\Correlation\Correlation;
use Datalog\Correlation\Http\CorrelationHttpClientDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CorrelationHttpClientDecoratorTest extends TestCase
{
    public function testAddsHeader(): void
    {
        Correlation::setId('test-id');

        $client = new MockHttpClient(function ($method, $url, $options) {
            $this->assertSame([CorrelationHttpClientDecorator::HEADER_CORRELATION_ID . ': test-id'], $options['normalized_headers']['x-correlation-id'] ?? null);

            return new MockResponse();
        });

        $decorator = new CorrelationHttpClientDecorator(new Correlation(), $client);

        $decorator->request('GET', 'http://example.com');
    }
}