<?php

namespace Acme\ServerBundle\Entity;

use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="picture_gallery")
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\PictureGalleryRepository")
 */
class PictureGallery implements RestEntityInterface
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
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Acme\ServerBundle\Entity\GalleryHasPicture", mappedBy="gallery")
     */
    private $galleries;

    public function __construct()
    {
        $this->galleries = new ArrayCollection();
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return PictureGallery
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
}
