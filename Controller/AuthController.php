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

use Bundle\DoctrineUserBundle\Form\LoginForm;
use Symfony\Framework\DoctrineBundle\Controller\DoctrineController;
use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\Validator\Validator;
use Symfony\Components\Validator\ConstraintValidatorFactory;
use Symfony\Components\Validator\Mapping\ClassMetadataFactory;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\Loader\LoaderChain;
use Symfony\Components\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Components\Validator\Mapping\Loader\XmlFileLoader;
use Symfony\Components\Validator\MessageInterpolator\XliffMessageInterpolator;

class AuthController extends DoctrineController
{

    public function loginAction()
    {
        $request = $this->getRequest();

        $messageFile = realpath($this->container->getParameter('kernel.root_dir').'/..').'/src/vendor/Symfony/src/Symfony/Components/Validator/Resources/i18n/messages.en.xml';
        $validationFile = realpath($this->container->getParameter('kernel.root_dir').'/..').'/src/vendor/Symfony/src/Symfony/Components/Form/Resources/config/validation.xml';

        $metadataFactory = new ClassMetadataFactory(new LoaderChain(array(
            new AnnotationLoader(),
            new XmlFileLoader($validationFile)
        )));
        $validatorFactory = new ConstraintValidatorFactory();
        $messageInterpolator = new NoValidationXliffMessageInterpolator($messageFile);
        $validator = new Validator($metadataFactory, $validatorFactory, $messageInterpolator);

        $form = new LoginForm('login', array(), $validator);

        if('POST' === $request->getMethod()) {
            $form->bind($request->get('login'));

            $username = $request->get('username');
            $password = $request->get('password');
            
            $user = $this->getEntityManager()
            ->getRepository('Bundle\DoctrineUserBundle\Entities\User')
            ->findOneByUsernameAndPassword($username, $password);

            if($user && $user->getIsActive())
            {
                $event = new Event($this, 'doctrine_user.login', array('user' => $user));
                $this->container->eventDispatcher->notify($event);

                return $this->redirect($this->generateUrl('loginSuccess'));
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
