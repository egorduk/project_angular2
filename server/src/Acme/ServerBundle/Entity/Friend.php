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
     * @var Friend
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\User", inversedBy="friends")
     * @ORM\JoinColumn(name="friend_id", referencedColumnName="id")
     */
    private $friend;

    /**
     * @var User
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
     * @return Friend
     */
    public function getFriend()
    {
        return $this->friend;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @param Friend $friend
     *
     * @return $this
     */
    public function setUserFriend($user, $friend)
    {
        $this->user = $user;
        $this->friend = $friend;

        return $this;
    }
}
