<?php

declare(strict_types=1);

namespace Datalog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionRequestProcessor implements ProcessorInterface
{
    private RequestStack $requestStack;
    private $sessionId;
    private $requestId;
    private $_server;
    private $_get;
    private $_post;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        if (null === $this->requestId) {
            $this->requestId = substr(uniqid(), -8);

            if ('cli' === PHP_SAPI) {
                $this->sessionId = getmypid();
            } else {
                $this->_server = [
                    'http.url' => (@$_SERVER['HTTP_HOST']) . '/' . (@$_SERVER['REQUEST_URI']),
                    'http.method' => @$_SERVER['REQUEST_METHOD'],
                    'http.useragent' => @$_SERVER['HTTP_USER_AGENT'],
                    'http.referer' => @$_SERVER['HTTP_REFERER'],
                    'http.x_forwarded_for' => @$_SERVER['HTTP_X_FORWARDED_FOR'],
                ];

                $this->_post = $this->clean($_POST);
                $this->_get = $this->clean($_GET);

                $this->sessionId = '????????';

                try {
                    $session = $this->requestStack->getSession();
                    if ($session->isStarted()) {
                        $this->sessionId = $session->getId();
                    }
                } catch (\RuntimeException) {
                }
            }
        }

        $record->extra = array_merge($record->extra, [
            'request_id' => $this->requestId,
            'session_id' => $this->sessionId,
        ]);

        if ('cli' !== PHP_SAPI) {
            $record->extra = array_merge($record->extra, [
                'http.url' => $this->_server['http.url'],
                'http.method' => $this->_server['http.method'],
                'http.useragent' => $this->_server['http.useragent'],
                'http.referer' => $this->_server['http.referer'],
                'http.x_forwarded_for' => $this->_server['http.x_forwarded_for'],
            ]);
        }

        return $record;
    }

    protected function clean($array): array
    {
        return array_filter(
            $array,
            static fn ($key) =>
                !(is_string($key)
                && (
                    false !== strpos($key, 'password') || false !== strpos($key, 'csrf_token')
                )),
            ARRAY_FILTER_USE_KEY,
        );
    }
}
