<?php

namespace Bundle\DoctrineUserBundle\DependencyInjection;
// Bundle\DoctrineUserBundle

use Symfony\Components\DependencyInjection\Loader;
use Symfony\Components\DependencyInjection\Loader\LoaderExtension;
use Symfony\Components\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Components\DependencyInjection\BuilderConfiguration;
use Symfony\Components\DependencyInjection\ContainerInterface;

class DoctrineUserExtension extends LoaderExtension
{
    protected $resources = array(
        'doctrine_user' => 'doctrine_user.xml',
    );

    public function configLoadDefaults()
    {
        $configuration = new BuilderConfiguration();

        $loader = new XmlFileLoader(__DIR__.'/../Resources/config');
        $configuration->merge($loader->load($this->resources['doctrine_user']));
        return $configuration;
    }

    public function configLoad($config)
    {
        $configuration = $this->configLoadDefaults();

        foreach ($config as $key => $value) {
            $configuration->setParameter('doctrine_user.config.' . $key, $value);
        }
        
		//die(print_r($config,true)); 
        return $configuration;
    }

    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/doctrine_user';
    }

    public function getAlias()
    {
        return 'doctrine_user';
    }
}
