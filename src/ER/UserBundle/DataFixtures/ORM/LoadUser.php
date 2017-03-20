<?php

namespace ER\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ER\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Les noms d'utilisateurs à créer
        $listNames = array('Alain', 'toto', 'foo');

        foreach ($listNames as $name) {
            // On crée l'utilisateur
            $user = new User();

            // Le nom d'utilisateur et le mot de passe sont identiques pour l'instant
            $user->setUsername($name);
            $user->setPassword('$2y$12$JC.E7NsYXkLD788AY3GaY.BnAVN4xRJKYuuA6xDem4fGUTfKM3wi.');

            // On ne se sert pas du sel pour l'instant
            $user->setSalt(md5(uniqid(null, true)));

            // On définit uniquement le role ROLE_USER qui est le role de base
            $user->setRoles(array('ROLE_ADMIN'));

            // On le persiste
            $manager->persist($user);
        }

        // On déclenche l'enregistrement
        $manager->flush();
    }
}
