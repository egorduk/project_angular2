<?php

namespace Acme\ServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PictureTag
 *
 * @ORM\Table(name="picture_tag", indexes={@ORM\Index(name="FK_picture_tag_picture", columns={"picture_id"}), @ORM\Index(name="FK_picture_tag_tag", columns={"tag_id"})})
 * @ORM\Entity
 */
class PictureTag
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
     * @var \Acme\ServerBundle\Entity\Tag
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\Tag")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     * })
     */
    private $tag;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tag
     *
     * @param \Acme\ServerBundle\Entity\Tag $tag
     * @return PictureTag
     */
    public function setTag(\Acme\ServerBundle\Entity\Tag $tag = null)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \Acme\ServerBundle\Entity\Tag 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set picture
     *
     * @param \Acme\ServerBundle\Entity\Picture $picture
     * @return PictureTag
     */
    public function setPicture(\Acme\ServerBundle\Entity\Picture $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return \Acme\ServerBundle\Entity\Picture 
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
