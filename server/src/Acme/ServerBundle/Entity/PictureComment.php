<?php

namespace Acme\ServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PictureComment
 *
 * @ORM\Table(name="picture_comment", indexes={@ORM\Index(name="FK_picture_comment_picture_id", columns={"picture_id"})})
 * @ORM\Entity
 */
class PictureComment
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_comment", type="datetime", nullable=false)
     */
    private $dateComment;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=50, nullable=false)
     */
    private $comment;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * Set dateComment
     *
     * @param \DateTime $dateComment
     * @return PictureComment
     */
    public function setDateComment($dateComment)
    {
        $this->dateComment = $dateComment;

        return $this;
    }

    /**
     * Get dateComment
     *
     * @return \DateTime 
     */
    public function getDateComment()
    {
        return $this->dateComment;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return PictureComment
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return PictureComment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
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
     * Set picture
     *
     * @param \Acme\ServerBundle\Entity\Picture $picture
     * @return PictureComment
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
