<?php

namespace Datalog\Processor;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionRequestProcessor
{
    private string|int|null $sessionId = null;
    private ?string $requestId = null;
    private array $additionalContext = [];

    public function __construct(
        private readonly SessionInterface $session,
    ) {}

    /**
     * @return mixed[]
     */
    public function processRecord(array $record): array
    {
        if (null === $this->requestId) {
            $this->requestId = substr(uniqid('', true), -8);

            if ('cli' === PHP_SAPI) {
                $this->sessionId = getmypid() ?: '????????';
            } else {
                try {
                    $this->session->start();
                    $this->sessionId = $this->session->getId();
                } catch (\Throwable) {
                    $this->sessionId = '????????';
                }

                $this->additionalContext = [
                    'http.url' => (@$_SERVER['HTTP_HOST']) . '/' . (@$_SERVER['REQUEST_URI']),
                    'http.method' => @$_SERVER['REQUEST_METHOD'],
                    'http.useragent' => @$_SERVER['HTTP_USER_AGENT'],
                    'http.referer' => @$_SERVER['HTTP_REFERER'],
                    'http.x_forwarded_for' => @$_SERVER['HTTP_X_FORWARDED_FOR'],
                ];
            }
        }

        $record['request_id'] = $this->requestId;
        $record['session_id'] = $this->sessionId;

        return array_merge($record, $this->additionalContext);
    }
}
