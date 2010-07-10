<?php

namespace Bundle\DoctrineUserBundle\Auth;

use Symfony\Framework\FoundationBundle\User as SymfonyUser;
use Symfony\Foundation\EventDispatcher;
use Symfony\Components\EventDispatcher\Event;
use Doctrine\ODM\MongoDB;

class Listener
{
    /**
     * @var SymfonyUser
     */
    protected $user;
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(SymfonyUser $user,  $em, EventDispatcher $eventDispatcher)
    {
        $this->user = $user;
        $this->em = $em;

        $eventDispatcher->connect('doctrine_user.login', array($this, 'listenToUserLoginEvent'));
        $eventDispatcher->connect('doctrine_user.logout', array($this, 'listenToUserLogoutEvent'));
    }

    public function listenToUserLoginEvent(Event $event)
    {
        $this->user->setAttribute('identity', $event['user']);

        $event['user']->setLastLogin(new \DateTime());
        $this->em->flush();
    }

    public function listenToUserLogoutEvent(Event $event)
    {
        $this->user->setAttribute('identity', null);
    }
}
