<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class LogLoginUserListener
{
    private $logger;

    private $authorizationChecker;

    public function __construct(LoggerInterface $logger, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->logger = $logger;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // user has just logged in
            $this->logger->info(sprintf('Użytkownik o e-mailu %s właśnie się zalogował ;)', $user->getEmail()));
        }

        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // user has logged in using remember_me cookie
            // can be useful in other app
        }
    }
}
