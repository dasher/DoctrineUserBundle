<?php

namespace Bundle\DoctrineUserBundle\Command;

require_once __DIR__."/../lib/Helper/DocumentManagerHelper.php";

use Symfony\Framework\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Components\Console\Input\InputArgument;
use Symfony\Components\Console\Input\InputOption;
use Symfony\Components\Console\Input\InputInterface;
use Symfony\Components\Console\Output\OutputInterface;
use Symfony\Components\Console\Output\Output;
use Bundle\DoctrineUserBundle\Documents\User;

/*
 * This file is part of the DoctrineUserBundle
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * CreateUserCommand.
 *
 * @package    Bundle
 * @subpackage DoctrineUserBundle
 * @author     Matthieu Bontemps <matthieu@knplabs.com>
 * @author     Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class CreateUserCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:user:create')
            ->setDescription(
                'Create a user.'
            )
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('super-admin', null, InputOption::PARAMETER_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::PARAMETER_NONE, 'Set the user as inactive'),
            ))
            ->addOption('em', null, InputOption::PARAMETER_OPTIONAL, 'The entity manager to use for this command.')
            ->setHelp(<<<EOT
The <info>doctrine:user:create</info> command creates a user:

  <info>./symfony doctrine:user:create matthieu</info>

This interactive shell will first ask you for a password.

You can alternatively specify the password as a second argument:

  <info>./symfony doctrine:user:create matthieu mypassword</info>

You can create a super admin via the super-admin flag:

  <info>./symfony doctrine:user:create admin --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>./symfony doctrine:user:create thibault --inactive</info>
  
EOT
        );
    }
	
	private function fetchODMManager() {
		
		$application = $this->application;		
		$container = $application->getKernel()->getContainer();
		
		//$this->container->setParameter('doctrine.odm.mongodb.default_server', 'gameDev01:27017');
		//$this->container->setParameter('doctrine.odm.mongodb.default_database','cityShadows');
		//$this->container->setParameter('doctrine.odm.mongodb.auto_generate_proxy_classes',true);
		//$this->container->setParameter('doctrine.odm.mongodb.document_dirs',array(0=>'/home/dasher/development/mongoSliceFeeds/src/Application/HelloBundle/Document'));
			
		$service = $container->getService("doctrine.odm.mongodb.document_manager");				
		
		$helperSet = $application->getHelperSet();
		$helperSet->set(new \Doctrine\ORM\Tools\Console\Helper\DocumentManagerHelper($service), 'odm');
	}

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$this->fetchODMManager();
        
        $user = new User();        
        $user->setUsername($input->getArgument('username'));
        $user->setPassword($input->getArgument('password'));
        $user->setIsSuperAdmin($input->getOption('super-admin'));
        $user->setIsActive(!$input->getOption('inactive'));

        $em = $this->getHelper('odm')->getDocumentManager();

        $em->persist($user);
        $em->flush();
        
        $output->writeln(sprintf('Created user <comment>%s</comment>', $user->getUsername()));
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if(null === $input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if(empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }
                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
}
