<?php

namespace Acme\ServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PictureLike.
 *
 * @ORM\Table(name="picture_like", indexes={@ORM\Index(name="FK_picture_like_picture_id", columns={"picture_id"}), @ORM\Index(name="FK_picture_like_user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\PictureLikeRepository")
 */
class PictureLike
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
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Acme\ServerBundle\Entity\Picture
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\Picture")
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
     * @param \Acme\ServerBundle\Entity\User $user
     *
     * @return PictureLike
     */
    public function setUser(\Acme\ServerBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \Acme\ServerBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set picture.
     *
     * @param \Acme\ServerBundle\Entity\Picture $picture
     *
     * @return PictureLike
     */
    public function setPicture(\Acme\ServerBundle\Entity\Picture $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return \Acme\ServerBundle\Entity\Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
