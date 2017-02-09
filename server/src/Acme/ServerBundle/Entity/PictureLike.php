<?php

namespace Acme\ServerBundle\Entity;

use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="picture_like", indexes={@ORM\Index(name="FK_picture_like_picture_id", columns={"picture_id"}), @ORM\Index(name="FK_picture_like_user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\PictureLikeRepository")
 */
class PictureLike implements RestEntityInterface
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
     * @var \Acme\ServerBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\User", inversedBy="users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Acme\ServerBundle\Entity\Picture
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\Picture", inversedBy="pictures")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     * })
     */
    private $picture;

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
     * Set user.
     *
     * @param User $user
     *
     * @return PictureLike
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set picture.
     *
     * @param Picture $picture
     *
     * @return PictureLike
     */
    public function setPicture(Picture $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
