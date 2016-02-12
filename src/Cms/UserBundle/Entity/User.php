<?php
namespace Cms\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="Cms\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface,  \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $roles ;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eraseCredentials;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="date", length=128)
     */
    private $dateBirthday;

    /**
     * @ORM\Column(type="text", length=512)
     */
    private $about;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return array($this->roles);
    }

    public function eraseCredentials()
    {

    }

    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Set eraseCredentials
     *
     * @param string $eraseCredentials
     * @return User
     */
    public function setEraseCredentials($eraseCredentials)
    {
        $this->eraseCredentials = $eraseCredentials;

        return $this;
    }

    /**
     * Get eraseCredentials
     *
     * @return string 
     */
    public function getEraseCredentials()
    {
        return $this->eraseCredentials;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set dateBirthday
     *
     * @param \DateTime $dateBirthday
     * @return User
     */
    public function setDateBirthday($dateBirthday)
    {
        $this->dateBirthday = $dateBirthday;

        return $this;
    }

    /**
     * Get dateBirthday
     *
     * @return \DateTime 
     */
    public function getDateBirthday()
    {
        return $this->dateBirthday;
    }

    /**
     * Set about
     *
     * @param string $about
     * @return User
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string 
     */
    public function getAbout()
    {
        return $this->about;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
