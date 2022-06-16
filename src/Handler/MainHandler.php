<?php

namespace Datalog\Handler;

use Datalog\Formatter\KeyValueFormatter;
use Datalog\Processor\SessionRequestProcessor;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class MainHandler extends FingersCrossedHandler
{
    public function __construct(
        \ArrayAccess $app,
        $stream,
        Level $activationStrategy = Level::Error,
        int $bufferSize = 0,
        bool $bubble = true,
        bool $stopBuffering = true,
        int $passthruLevel = null
    ) {
        $basicHandler = new StreamHandler($stream, Level::Debug);
        $basicHandler->setFormatter(new KeyValueFormatter());

        $sessionProcessor = new SessionRequestProcessor($app['session']);
        $basicHandler->pushProcessor([$sessionProcessor, 'processRecord']);

        //Wrap the stream in fingers crossed
        parent::__construct($basicHandler, $activationStrategy, $bufferSize, $bubble, $stopBuffering, $passthruLevel);
    }
}
