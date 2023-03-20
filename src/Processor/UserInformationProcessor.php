<?php

declare(strict_types=1);

namespace Datalog\Processor;

use Monolog\Processor\ProcessorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserInformationProcessor implements ProcessorInterface
{
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(array $record): array
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        if ($user instanceof UserInterface && method_exists($user, 'getId')) {
            $record['extra']['user-id'] = $user->getId();
        }

        return $record;
    }
}
