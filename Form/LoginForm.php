<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Components\Form\Form;
use Symfony\Components\Form\TextField;
use Symfony\Components\Form\PasswordField;
use Symfony\Components\Validator\Validator;
use Symfony\Components\Validator\ConstraintValidatorFactory;
use Symfony\Components\Validator\Mapping\ClassMetadataFactory;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\Loader\LoaderChain;
use Symfony\Components\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Components\Validator\Mapping\Loader\XmlFileLoader;
use Bundle\MiamBundle\Validator\NoValidationXliffMessageInterpolator;

/**
 * Form used to ask username and password to the user
 *
 * @author     Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class LoginForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('username'));
        $this->add(new PasswordField('password'));
    }
}
