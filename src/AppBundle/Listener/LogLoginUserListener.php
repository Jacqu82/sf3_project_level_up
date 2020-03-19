<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class LogLoginUserListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $sessionKey = $event->getRequest()->getSession()->get('_security_main');
        if (null === $sessionKey) {
            return;
        }

        $user = unserialize($sessionKey)->getUser();
        if ($user instanceof User) {
            $this->logger->info(sprintf('Użytkownik o e-mailu %s właśnie się zalogował ;)', $user->getEmail()));
        }
    }
}
