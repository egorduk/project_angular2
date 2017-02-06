<?php

namespace Acme\ServerBundle\Entity;

use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\UserRepository")
 */
class User implements RestEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=50, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=50, nullable=false)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=50, nullable=false)
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=255, nullable=false)
     */
    private $info;

    /**
     * @var string
     *
     * @ORM\Column(name="page_photo", type="string", length=50, nullable=false)
     */
    private $pagePhoto;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=415, nullable=false)
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity="Acme\ServerBundle\Entity\Friend", mappedBy="friend")
     */
    private $friends;

    /**
     * @ORM\OneToMany(targetEntity="Acme\ServerBundle\Entity\Friend", mappedBy="user")
     */
    private $users;

    private $regInfo;

    /**
     * User constructor.
     *
     * @param string $avatar
     */
    public function __construct()
    {
        $this->avatar = 'default.png';
        $this->pagePhoto = 'default.png';
        $this->info = 'No info';
        $this->token = '';

        $this->users = new ArrayCollection();
        $this->friends = new ArrayCollection();
    }

    /**
     * Set email.
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
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password.
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
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set login.
     *
     * @param string $login
     *
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set avatar.
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set info.
     *
     * @param string $info
     *
     * @return User
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info.
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set pagePhoto.
     *
     * @param string $pagePhoto
     *
     * @return User
     */
    public function setPagePhoto($pagePhoto)
    {
        $this->pagePhoto = $pagePhoto;

        return $this;
    }

    /**
     * Get pagePhoto.
     *
     * @return string
     */
    public function getPagePhoto()
    {
        return $this->pagePhoto;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    public function __toString()
    {
        return 'user';
    }

    /**
     * @return mixed
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @param mixed $friends
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getRegInfo()
    {
        return $this->regInfo;
    }

    /**
     * @param mixed $regInfo
     */
    public function setRegInfo($regInfo)
    {
        $this->regInfo = $regInfo;
    }
}
