<?php

declare(strict_types=1);

namespace Datalog\Correlation\Http;

use Datalog\Correlation\Correlation;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CorrelationHttpClientDecorator implements HttpClientInterface
{
    use DecoratorTrait;

    public const HEADER_CORRELATION_ID = 'X-Correlation-ID';

    private Correlation $correlation;

    public function __construct(
        Correlation $correlation,
        ?HttpClientInterface $client = null,
    ) {
        $this->correlation = $correlation;
        $this->client = $client ?? HttpClient::create();
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        if (!isset($options['headers']) || !array_key_exists(self::HEADER_CORRELATION_ID, $options['headers'])) {
            $options['headers'][self::HEADER_CORRELATION_ID] = $this->correlation->getId();
        }

        return $this->client->request($method, $url, $options);
    }
}
