<?php
namespace Cms\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Cms\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank(
     *     message = "Uzupełnij wszystkie pola."
     * )
     * @ORM\Column(type="string", length=64)
     */
    private $username;

    /**
     * @Assert\NotBlank(
     *     message = "Uzupełnij wszystkie pola."
     * )
     * @Assert\Email(
     *     message = "Podany przez ciebie email {{ value }} nie jest prawidłowy.",
     *     checkMX = true
     * )
     * @ORM\Column(type="string", length= 64)
     */
    private $email;

    /**
     * @Assert\NotBlank(
     *     message = "Uzupełnij wszystkie pola."
     * )
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank(
     *     message = "Uzupełnij wszystkie pola."
     * )
     * @Assert\DateTime(
     *     message="Podana przez Ciebię data urodzin nie jest prawidłowa"
     * )
     * @ORM\Column(type="date", length=128)
     */
    private $dateOfBirthday;

    /**
     * @Assert\NotBlank(
     *     message = "Uzupełnij wszystkie pola."
     * )
     * @ORM\Column(type="text")
     */
    private $about;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $salt;

    /**
     * @ORM\ManyToOne(targetEntity="Cms\UserBundle\Entity\Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eraseCredentials;

    /**
     * @ORM\Column(name="is_active", type="boolean",  options={"default": 0})
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Image()
     */
    private $profilePicturePath;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $activatedHash;


    public function __construct()
    {
        $this->setActivatedHash(bin2hex(random_bytes(36)));
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
     * Get eraseCredentials
     *
     * @return string
     */
    public function getEraseCredentials()
    {
        return $this->eraseCredentials;
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


    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set dateOfBirthday
     *
     * @param \DateTime $dateOfBirthday
     *
     * @return User
     */
    public function setDateOfBirthday($dateOfBirthday)
    {
        $this->dateOfBirthday = $dateOfBirthday;

        return $this;
    }

    /**
     * Get dateOfBirthday
     *
     * @return \DateTime
     */
    public function getDateOfBirthday()
    {
        return $this->dateOfBirthday;
    }

    /**
     * Set about
     *
     * @param string $about
     *
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

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }


    /**
     * Set eraseCredentials
     *
     * @param string $eraseCredentials
     *
     * @return User
     */
    public function setEraseCredentials($eraseCredentials)
    {
        $this->eraseCredentials = $eraseCredentials;

        return $this;
    }


    /**
     * Set roles
     *
     * @param \Cms\UserBundle\Entity\Role $roles
     *
     * @return User
     */
    public function setRoles(\Cms\UserBundle\Entity\Role $roles = null)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Set profilePicturePath
     *
     * @param string $profilePicturePath
     *
     * @return User
     */
    public function setProfilePicturePath($profilePicturePath)
    {
        $this->profilePicturePath = $profilePicturePath;

        return $this;
    }

    /**
     * Get profilePicturePath
     *
     * @return string
     */
    public function getProfilePicturePath()
    {
        return $this->profilePicturePath;
    }

    /**
     * Serialization is required to FileUploader
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
            $this->roles
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
            $this->roles
            ) = unserialize($serialized);
    }

    /**
     * @ORM\PreRemove
     */
    public function removeProfilePicture()
    {
        $fileAbsolutePath = __DIR__.'/../../../../web/uploads/profile_picture/'.$this->getProfilePicturePath();

        if(file_exists($fileAbsolutePath)){
            unlink($fileAbsolutePath);
        }
    }

    /**
     * Set activatedHash
     *
     * @param string $activatedHash
     *
     * @return User
     */
    public function setActivatedHash($activatedHash)
    {
        $this->activatedHash = $activatedHash;

        return $this;
    }

    /**
     * Get activatedHash
     *
     * @return string
     */
    public function getActivatedHash()
    {
        return $this->activatedHash;
    }
}
