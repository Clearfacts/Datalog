<?php

declare(strict_types=1);

namespace Datalog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionRequestProcessor implements ProcessorInterface
{
    public const KEY_REQUEST_ID = 'request_id';
    public const KEY_SESSION_ID = 'session_id';
    public const KEY_HTTP_URL = 'http.url';
    public const KEY_HTTP_METHOD = 'http.method';
    public const KEY_HTTP_USERAGENT = 'http.useragent';
    public const KEY_HTTP_REFERER = 'http.referer';
    public const KEY_HTTP_X_FORWARDED_FOR = 'http.x_forwarded_for';

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
                    self::KEY_HTTP_URL => (@$_SERVER['HTTP_HOST']) . '/' . (@$_SERVER['REQUEST_URI']),
                    self::KEY_HTTP_METHOD => @$_SERVER['REQUEST_METHOD'],
                    self::KEY_HTTP_USERAGENT => @$_SERVER['HTTP_USER_AGENT'],
                    self::KEY_HTTP_REFERER => @$_SERVER['HTTP_REFERER'],
                    self::KEY_HTTP_X_FORWARDED_FOR => @$_SERVER['HTTP_X_FORWARDED_FOR'],
                ];

                $this->_post = $this->clean($_POST);
                $this->_get = $this->clean($_GET);

                $this->sessionId = '????????';

                try {
                    $session = $this->requestStack->getSession();
                    if ($session->isStarted()) {
                        $this->sessionId = $session->getId();
                    }
                } catch (\RuntimeException|\LogicException) {
                }
            }
        }

        $record->extra = array_merge($record->extra, [
            self::KEY_REQUEST_ID => $this->requestId,
            self::KEY_SESSION_ID => $this->sessionId,
        ]);

        if ('cli' !== PHP_SAPI) {
            $record->extra = array_merge($record->extra, [
                self::KEY_HTTP_URL => $this->_server[self::KEY_HTTP_URL],
                self::KEY_HTTP_METHOD => $this->_server[self::KEY_HTTP_METHOD],
                self::KEY_HTTP_USERAGENT => $this->_server[self::KEY_HTTP_USERAGENT],
                self::KEY_HTTP_REFERER => $this->_server[self::KEY_HTTP_REFERER],
                self::KEY_HTTP_X_FORWARDED_FOR => $this->_server[self::KEY_HTTP_X_FORWARDED_FOR],
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
