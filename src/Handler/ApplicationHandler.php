<?php

namespace Datalog\Handler;

use Datalog\Formatter\KeyValueFormatter;
use Datalog\Processor\SessionRequestProcessor;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ApplicationHandler extends StreamHandler
{
    public function __construct(\ArrayAccess $app, $stream = 'php://stdout', int $level = Logger::NOTICE, bool $bubble = true, int $filePermission = null, bool $useLocking = false)
    {
        parent::__construct($stream, $level, $bubble, $filePermission, $useLocking);

        $this->setFormatter(new KeyValueFormatter());

        $sessionProcessor = new SessionRequestProcessor($app['session']);
        $this->pushProcessor([$sessionProcessor, 'processRecord']);
    }
}
