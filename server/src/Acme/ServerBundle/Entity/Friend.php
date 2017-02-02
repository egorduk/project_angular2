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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="friend_id", type="integer", nullable=false)
     */
    private $friendId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;


    /**
     * Set friendId
     *
     * @param integer $friendId
     * @return Friend
     */
    public function setFriendId($friendId)
    {
        $this->friendId = $friendId;

        return $this;
    }

    /**
     * Get friendId
     *
     * @return integer 
     */
    public function getFriendId()
    {
        return $this->friendId;
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
     * Set user
     *
     * @param int $user
     * @return Friend
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
