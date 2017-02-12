<?php

namespace Acme\ServerBundle\Entity;

use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Table(name="picture")
 * @ORM\Entity(repositoryClass="Acme\ServerBundle\Repository\PictureRepository")
 */
class Picture implements RestEntityInterface
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Acme\ServerBundle\Entity\User", inversedBy="users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upload", type="datetime", nullable=false)
     */
    private $dateUpload;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=50, nullable=false)
     */
    private $filename;

    /**
     * @var int
     *
     * @ORM\Column(name="resize_height", type="smallint", nullable=false)
     */
    private $resizeHeight;

    /**
     * @var int
     *
     * @ORM\Column(name="resize_width", type="smallint", nullable=false)
     */
    private $resizeWidth;

    /**
     * @var int
     *
     * @ORM\Column(name="is_show_host", type="smallint", nullable=false)
     */
    private $isShowHost;

    /**
     * @ORM\OneToMany(targetEntity="Acme\ServerBundle\Entity\PictureComment", mappedBy="picture")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="Acme\ServerBundle\Entity\PictureLike", mappedBy="picture")
     */
    private $pictures;

    private $file;

    public function setFile(File $file)
    {
        $this->file = $file;
    }

    public function __construct()
    {
        $this->setDateUploadIsShowHost();

        $this->comments = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Picture
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
     * Set dateUpload.
     *
     * @param \DateTime $dateUpload
     *
     * @return Picture
     */
    public function setDateUpload($dateUpload)
    {
        $this->dateUpload = $dateUpload;

        return $this;
    }

    /**
     * Get dateUpload.
     *
     * @return \DateTime
     */
    public function getDateUpload()
    {
        return $this->dateUpload;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return Picture
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set resizeHeight.
     *
     * @param int $resizeHeight
     *
     * @return Picture
     */
    public function setResizeHeight($resizeHeight)
    {
        $this->resizeHeight = $resizeHeight;

        return $this;
    }

    /**
     * Get resizeHeight.
     *
     * @return int
     */
    public function getResizeHeight()
    {
        return $this->resizeHeight;
    }

    /**
     * Set resizeWidth.
     *
     * @param int $resizeWidth
     *
     * @return Picture
     */
    public function setResizeWidth($resizeWidth)
    {
        $this->resizeWidth = $resizeWidth;

        return $this;
    }

    /**
     * Get resizeWidth.
     *
     * @return int
     */
    public function getResizeWidth()
    {
        return $this->resizeWidth;
    }

    /**
     * Set isShowHost.
     *
     * @param int $isShowHost
     *
     * @return Picture
     */
    public function setIsShowHost($isShowHost)
    {
        $this->isShowHost = $isShowHost;

        return $this;
    }

    /**
     * Get isShowHost.
     *
     * @return int
     */
    public function getIsShowHost()
    {
        return $this->isShowHost;
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
     * @return $this
     */
    public function setDateUploadIsShowHost()
    {
        $this->dateUpload = new \DateTime();
        $this->isShowHost = 1;

        return $this;
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

    /**
     * @param string $name
     * @param int    $isShowHost
     *
     * @return $this
     */
    public function setNameIsShowHost($name, $isShowHost)
    {
        $this->name = $name;
        $this->isShowHost = $isShowHost;

        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     *
     * @return $this
     */
    public function setResizeWidthHeight($width, $height)
    {
        $this->resizeWidth = $width;
        $this->resizeHeight = $height;

        return $this;
    }
}
