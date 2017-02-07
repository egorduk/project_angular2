<?php

namespace Acme\ServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="picture_comment", indexes={@ORM\Index(name="FK_picture_comment_picture_id", columns={"picture_id"})})
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\PictureCommentRepository")
 */
class PictureComment
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_comment", type="datetime", nullable=false)
     */
    private $dateComment;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=50, nullable=false)
     */
    private $comment;

    /**
     * @var \Acme\ServerBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\User", inversedBy="users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \Acme\ServerBundle\Entity\Picture
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\Picture", inversedBy="comments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     * })
     */
    private $picture;

    /**
     * PictureComment constructor.
     * @param \DateTime $dateComment
     */
    public function __construct()
    {
        $this->dateComment = new \DateTime();
    }


    /**
     * Set dateComment.
     *
     * @param \DateTime $dateComment
     *
     * @return PictureComment
     */
    public function setDateComment($dateComment)
    {
        $this->dateComment = $dateComment;

        return $this;
    }

    /**
     * Get dateComment.
     *
     * @return \DateTime
     */
    public function getDateComment()
    {
        return $this->dateComment;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return PictureComment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
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

    /**
     * Set picture.
     *
     * @param \Acme\ServerBundle\Entity\Picture $picture
     *
     * @return PictureComment
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

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
