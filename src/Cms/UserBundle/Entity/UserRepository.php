<?php
namespace Cms\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function loadUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameters(array('username' => $username))
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function getAllUsers()
    {
        $users = $this->createQueryBuilder('u')
            ->select('u.username, u.email, r.name as roleName, r.role, u.dateOfBirthday, u.about, u.isActive, u.id')
            ->join('u.roles','r')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getArrayResult();

         return $users;
    }
}