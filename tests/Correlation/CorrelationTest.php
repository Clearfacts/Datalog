<?php

declare(strict_types=1);

namespace Tests\Datalog\Correlation;

use Datalog\Correlation\Correlation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Datalog\TestCase;

class CorrelationTest extends TestCase
{
    private Correlation $correlation;
    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->correlation = new Correlation($this->requestStack);
        $this->correlation::setId(null);
    }

    public function testInit(): void
    {
        $this->assertEmpty($this->correlation::getId());

        $this->correlation->init();

        $this->assertNotEmpty($this->correlation::getId());

        $firstId = $this->correlation::getId();
        $this->correlation->init();

        $this->assertSame($firstId, $this->correlation::getId());
    }

    public function testInitPrefersRequestStack(): void
    {
        $_SERVER['HTTP_X_CORRELATION_ID'] = 'test server';
        $request = new Request();
        $request->headers->set('X-Correlation-ID', 'test request');
        $this->requestStack->push($request);

        $this->correlation->init();

        $this->assertSame('test request', $this->correlation::getId());
    }

    public function testInitFallsBackToGlobals(): void
    {
        $_SERVER['HTTP_X_CORRELATION_ID'] = 'test server';
        $request = new Request();
        $this->requestStack->push($request);

        $this->correlation->init();

        $this->assertSame('test server', $this->correlation::getId());
    }
}