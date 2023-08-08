<?php

declare(strict_types=1);

namespace Datalog\Correlation\Messenger;

use Datalog\Correlation\Correlation;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class CorrelationIdSerializer implements SerializerInterface
{
    public function __construct(
        private Correlation $correlation,
        private SerializerInterface $serializer,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        if (isset($encodedEnvelope['headers']['correlation_id'])) {
            $this->correlation::setId($encodedEnvelope['headers']['correlation_id']);
        }

        return $this->serializer->decode($encodedEnvelope);
    }

    public function encode(Envelope $envelope): array
    {
        $encodedEnvelope = $this->serializer->encode($envelope);
        $encodedEnvelope['headers']['correlation_id'] = $this->correlation->getId();

        return $encodedEnvelope;
    }
}
