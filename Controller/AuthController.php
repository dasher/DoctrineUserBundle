<?php

/**
 * This file is part of the Symfony framework.
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Framework\DoctrineBundle\Controller\DoctrineController;
use Symfony\Components\EventDispatcher\Event;

class AuthController extends DoctrineController
{
	
	public function attachDocumentDirs() {
            
		$documentDirs = $this->container->getParameter("doctrine.odm.mongodb.document_dirs");
	    $documentDirs = array_merge($documentDirs, array(0=>__DIR__.'/../Documents'));            
	    $this->container->setParameter('doctrine.odm.mongodb.document_dirs',$documentDirs);
		
	}

    public function loginAction()
    {
        $request = $this->getRequest();

        if('POST' === $request->getMethod()) {
            $username = $request->get('username');
            $password = $request->get('password');
            
            $odm = $this->container->getService("doctrine.odm.mongodb.document_manager");            
            $this->attachDocumentDirs();
            
            $user = $odm->getRepository('Bundle\DoctrineUserBundle\Documents\User')->findOneByUsernameAndPassword($username, $password);
            
//            $user = $this->getEntityManager()
//            ->getRepository('Bundle\DoctrineUserBundle\Entities\User')
//            ->findOneByUsernameAndPassword($username, $password);

            if($user && $user->getIsActive())
            {
                $event = new Event($this, 'doctrine_user.login', array('user' => $user));
                $this->container->eventDispatcher->notify($event);
                
                $successRoute = $this->container->getParameter("doctrine_user.config.route.success");
//                if (empty($successRoute)) {
//                	$successRoute = $this->container->getParameter("doctrine_user.route.default.success");
//                }

                return $this->redirect($this->generateUrl($successRoute));
            }
            else
            {
                $this->getUser()->setFlash('loginError', true);
            }
        }

        $view = $this->container->getParameter('doctrine_user.view.login');
        return $this->render($view, array());
    }

    public function logoutAction()
    {
        if($user = $this->getUser()->getAttribute('identity'))
        {
            $event = new Event($this, 'doctrine_user.logout', array('user' => $user));
            $this->container->eventDispatcher->notify($event);
        }

        return $this->redirect($this->generateUrl('login'));
    }

    public function successAction()
    {
        $identity = $this->getUser()->getAttribute('identity');

        $view = $this->container->getParameter('doctrine_user.view.success');
        return $this->render($view, array(
            'identity' => $identity,
        ));
    }

}
