<?php

/**
 * This file is part of the Symfony framework.
 *
 * (c) David Ashwood <dasher@inspiredthinking.co.uk>
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Documents;

use Doctrine\ODM\MongoDB;
use Doctrine\ODM\MongoDB\DocumentRepository;

class UserRepository extends MongoDB\DocumentRepository
{

    /**
     * Find a user by its username and password
     * @param   string  $username
     * @param   string  $password
     * @return  User or null if user does not exist
     */
    public function findOneByUsernameAndPassword($username, $password) {
        $user = $this->findOneByUsername($username);
        
        if($user && $user->checkPassword($password)) {
            return $user;
        }
    }

    /**
     * Find a user by its username
     * @param   string  $username
     * @return  User or null if user does not exist
     */
    public function findOneByUsername($username) {
    	return $this->findOne(array('username' => $username));
//    	$this->query("find all Documents\User where username = 'jwage'");
//        return $this->findOneBy(array('username' => $username));
    }

}

