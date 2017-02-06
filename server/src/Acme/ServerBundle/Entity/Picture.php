<?php

namespace Acme\ServerBundle\Entity;

use Acme\ServerBundle\Model\RestEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     *
     * @Assert\NotBlank()
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
     *
     * @Assert\NotBlank()
     */
    private $filename;

    /**
     * @var int
     *
     * @ORM\Column(name="resize_height", type="smallint", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $resizeHeight;

    /**
     * @var int
     *
     * @ORM\Column(name="resize_width", type="smallint", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $resizeWidth;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_show_host", type="boolean", nullable=false)
     */
    private $isShowHost;

    public function __construct()
    {
        $this->setDateUploadAndIsShowHost();
    }

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return Picture
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
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
     * @param bool $isShowHost
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
     * @return bool
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
    public function setDateUploadAndIsShowHost()
    {
        $this->setDateUpload(new \DateTime());
        $this->setIsShowHost(true);

        return $this;
    }
}
