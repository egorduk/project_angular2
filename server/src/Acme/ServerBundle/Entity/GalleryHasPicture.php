<?php

namespace Acme\ServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GalleryHasPicture.
 *
 * @ORM\Table(name="gallery_has_picture", indexes={@ORM\Index(name="FK_gallery_has_picture_picture_gallery", columns={"gallery_id"}), @ORM\Index(name="FK_gallery_has_picture_picture", columns={"picture_id"}), @ORM\Index(name="FK_gallery_has_picture_user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\GalleryHasPictureRepository")
 */
class GalleryHasPicture
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
     * @var \Acme\ServerBundle\Entity\PictureGallery
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\PictureGallery")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     * })
     */
    private $gallery;

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
     * @return GalleryHasPicture
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
     * Set gallery.
     *
     * @param \Acme\ServerBundle\Entity\PictureGallery $gallery
     *
     * @return GalleryHasPicture
     */
    public function setGallery(\Acme\ServerBundle\Entity\PictureGallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery.
     *
     * @return \Acme\ServerBundle\Entity\PictureGallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set picture.
     *
     * @param \Acme\ServerBundle\Entity\Picture $picture
     *
     * @return GalleryHasPicture
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
