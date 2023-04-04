<?php

declare(strict_types=1);

namespace Tests\Datalog\Processor;

use Datalog\Processor\SessionRequestProcessor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tests\Datalog\TestCase;

class SessionRequestProcessorTest extends TestCase
{
    private SessionRequestProcessor $processor;

    public function setUp(): void
    {
        $this->processor = new SessionRequestProcessor(
            $this->createMock(SessionInterface::class)
        );
    }

    public function testCleansParamKeys(): void
    {
        $params = [
            'foo' => 'bar',
            'test password test' => 'password',
            1 => 'one',
            'tester csrf_token tester' => 'csrf_token',
            'baz' => [
                'qux' => 'quux',
            ],
            'password' => 'password',
            'password test' => 'password',
        ];

        $cleanedParams = self::callPrivateMethod($this->processor, 'clean', $params);

        $this->assertSame([
            'foo' => 'bar',
            1 => 'one',
            'baz' => [
                'qux' => 'quux',
            ],
        ], $cleanedParams);
    }
}