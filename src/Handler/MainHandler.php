<?php

namespace Datalog\Handler;

use Datalog\Formatter\KeyValueFormatter;
use Datalog\Processor\SessionRequestProcessor;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MainHandler extends FingersCrossedHandler
{
    public function __construct(\ArrayAccess $app, $stream, $activationStrategy = Logger::ERROR, int $bufferSize = 0, bool $bubble = true, bool $stopBuffering = true, int $passthruLevel = null)
    {
        $basicHandler = new StreamHandler($stream, \Monolog\Logger::DEBUG);
        $basicHandler->setFormatter(new KeyValueFormatter());

        $sessionProcessor = new SessionRequestProcessor($app['session']);
        $basicHandler->pushProcessor([$sessionProcessor, 'processRecord']);

        //Wrap the stream in fingers crossed
        parent::__construct($basicHandler, $activationStrategy, $bufferSize, $bubble, $stopBuffering, $passthruLevel);
    }
}