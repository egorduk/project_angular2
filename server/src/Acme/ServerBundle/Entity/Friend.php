<?php

namespace Acme\ServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Friend
 *
 * @ORM\Table(name="friend", indexes={@ORM\Index(name="FK_friend_user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\FriendRepository")
 */
class Friend
{
    /**
     * @var integer
     *
     * @ORM\Column(name="friend_id", type="integer", nullable=false)
     */
    private $friendId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Acme\ServerBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



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
     * @param \Acme\ServerBundle\Entity\User $user
     * @return Friend
     */
    public function setUser(\Acme\ServerBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Acme\ServerBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
