<?php

declare(strict_types=1);

namespace Datalog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserInformationProcessor implements ProcessorInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        if ($user instanceof UserInterface && method_exists($user, 'getId')) {
            $record->extra = array_merge($record->extra, ['user-id' => $user->getId()]);
        }

        return $record;
    }
}
