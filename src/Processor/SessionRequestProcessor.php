<?php

namespace Datalog\Processor;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionRequestProcessor
{
    private $session;
    private $sessionId;
    private $requestId;
    private $_server;
    private $_get;
    private $_post;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function processRecord(array $record)
    {
        if (null === $this->requestId) {
            if ('cli' === php_sapi_name()) {
                $this->sessionId = getmypid();
            } else {
                try {
                    $this->session->start();
                    $this->sessionId = $this->session->getId();
                } catch (\RuntimeException $e) {
                    $this->sessionId = '????????';
                }
            }
            $this->requestId = substr(uniqid(), -8);
            $this->_server = [
                'http.url' => (@$_SERVER['HTTP_HOST']) . '/' . (@$_SERVER['REQUEST_URI']),
                'http.method' => @$_SERVER['REQUEST_METHOD'],
                'http.useragent' => @$_SERVER['HTTP_USER_AGENT'],
                'http.referer' => @$_SERVER['HTTP_REFERER'],
                'http.x_forwarded_for' => @$_SERVER['HTTP_X_FORWARDED_FOR'],
            ];
            $this->_post = $this->clean($_POST);
            $this->_get = $this->clean($_GET);
        }
        $record['http.request_id'] = $this->requestId;
        $record['http.session_id'] = $this->sessionId;
        $record['http.url'] = $this->_server['http.url'];
        $record['http.method'] = $this->_server['http.method'];
        $record['http.useragent'] = $this->_server['http.useragent'];
        $record['http.referer'] = $this->_server['http.referer'];
        $record['http.x_forwarded_for'] = $this->_server['http.x_forwarded_for'];

        return $record;
    }

    protected function clean($array)
    {
        $toReturn = [];
        foreach (array_keys($array) as $key) {
            if (false !== strpos($key, 'password')) {
                // Do not add
            } elseif (false !== strpos($key, 'csrf_token')) {
                // Do not add
            } else {
                $toReturn[$key] = $array[$key];
            }
        }

        return $toReturn;
    }
}
