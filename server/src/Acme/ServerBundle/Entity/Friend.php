<?php

namespace Acme\ServerBundle\Entity;

use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="friend", indexes={@ORM\Index(name="FK_friend_user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\FriendRepository")
 */
class Friend implements RestEntityInterface
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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\User", inversedBy="friends")
     * @ORM\JoinColumn(name="friend_id", referencedColumnName="id")
     */
    private $friend;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\User", inversedBy="users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * @return int
     */
    public function getFriend()
    {
        return $this->friend;
    }

    /**
     * @param int $friend
     */
    public function setFriend($friend)
    {
        $this->friend = $friend;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
