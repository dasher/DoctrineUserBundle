<?php

namespace Bundle\DoctrineUserBundle\Auth;

use Symfony\Components\EventDispatcher\EventDispatcher;
use Symfony\Framework\FoundationBundle\Controller;
use Symfony\Components\HttpKernel\Exception;
use Symfony\Framework\FoundationBundle\User as SymfonyUser;
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
    
    protected $cm;

    public function __construct(SymfonyUser $user,  $em, EventDispatcher $eventDispatcher, Controller\ControllerManager $cm)
    {
        $this->user = $user;
        $this->em = $em;
        $this->cm = $cm;

        $eventDispatcher->connect('doctrine_user.login', array($this, 'listenToUserLoginEvent'));
        $eventDispatcher->connect('doctrine_user.logout', array($this, 'listenToUserLogoutEvent'));
        $eventDispatcher->connect('core.exception', array($this, 'listenForUnauthException'), 1);
    }
    
    public function listenForUnauthException(Event $event) {
    	$exception = $event['exception'];
    	
    	if (($exception instanceof UnauthorizedHttpException) || ($exception->getCode() == 401)) {
    		$cDetails = $this->cm->findController("DoctrineUserBundle:Auth:login");
    		
    		$controller = $cDetails[0];
    		$method = $cDetails[1];
    		$response = $controller->$method();
    		
        	$event->setReturnValue($response);
        	//$event->setProcessed(true);
        	
        	return true;
    	}
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
