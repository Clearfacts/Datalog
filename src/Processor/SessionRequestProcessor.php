<?php

declare(strict_types=1);

namespace Datalog\Processor;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionRequestProcessor
{
    private SessionInterface $session;
    private $sessionId;
    private $requestId;
    private $_server;
    private $_get;
    private $_post;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function processRecord(array $record): array
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

                try {
                    $this->session->start();
                    $this->sessionId = $this->session->getId();
                } catch (\RuntimeException $e) {
                    $this->sessionId = '????????';
                }
            }
        }

        $record['request_id'] = $this->requestId;
        $record['session_id'] = $this->sessionId;

        if ('cli' !== PHP_SAPI) {
            $record['http.url'] = $this->_server['http.url'];
            $record['http.method'] = $this->_server['http.method'];
            $record['http.useragent'] = $this->_server['http.useragent'];
            $record['http.referer'] = $this->_server['http.referer'];
            $record['http.x_forwarded_for'] = $this->_server['http.x_forwarded_for'];
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
