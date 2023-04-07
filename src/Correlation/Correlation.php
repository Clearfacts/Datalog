<?php
declare(strict_types=1);

namespace Datalog\Correlation;

use Symfony\Component\HttpFoundation\RequestStack;

class Correlation
{
    private ?RequestStack $requestStack;
    private static ?string $correlationId = null;

    public function __construct(?RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    public function init(): void
    {
        if (!self::$correlationId) {
            self::$correlationId = $this->getIdFromRequestStack()
                ?? $this->getIdFromGlobals()
                ?? uniqid('cf', true);
        }
    }

    public static function setId(?string $correlationId): void
    {
        self::$correlationId = $correlationId;
    }

    public static function getId(): ?string
    {
        return self::$correlationId;
    }

    public function getIdFromRequestStack(): ?string
    {
        if (!$this->requestStack) {
            return null;
        }

        $request = $this->requestStack->getMainRequest();

        if (!$request) {
            return null;
        }

        return $request->headers->get('X-Correlation-ID');
    }

    public function getIdFromGlobals(): ?string
    {
        return $_SERVER['HTTP_X_CORRELATION_ID']
            ?? $_SERVER['X_CORRELATION_ID']
            ?? $_SERVER['CORRELATION_ID']
            ?? $_ENV['CORRELATION_ID']
            ?? null;
    }
}